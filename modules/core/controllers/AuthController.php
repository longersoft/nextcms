<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-01-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_AuthController extends Zend_Controller_Action
{
	const SESSION_LOGIN_ATTEMPTS = 'Core_AuthController_loginAttempts';
	
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init() 
	{
		Zend_Layout::getMvcInstance()
				   ->setLayoutPath(APP_ROOT_DIR . DS . 'templates' . DS . 'admin' . DS . 'layouts')
				   ->setLayout('auth');
	}
	
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * This action is called when user access a not allowed back-end section
	 * 
	 * @return void
	 */
	public function denyAction()
	{
	}
	
	/**
	 * Login
	 * 
	 * @return void
	 */
	public function loginAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$auth	   = Zend_Auth::getInstance();
		$loginUrl  = $this->view->serverUrl() . $this->view->url(array(), 'core_auth_login');
		$messenger = $this->_helper->getHelper('FlashMessenger');
		
		// Redirect to dashboard if user has logged in already
		if ($auth->hasIdentity()) {
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index'));
			exit;
		}
		
		$openIdEnabled = Core_Services_Config::get('core', 'register_openid_enabled', 'false') == 'true';
		
		// If user has tried to login in number of attempts exceeds number of allowed attempts,
		// then show a captcha
		$session = new Zend_Session_Namespace(self::SESSION_LOGIN_ATTEMPTS);
		if (!$session->numLoginAttempts) {
			$session->numLoginAttempts = 0;
		}
		if ($session->numLoginAttempts > Core_Services_Config::get('core', 'num_login_attempts', 3)) {
			$captcha = Core_Services_Captcha::getCaptcha();
			if (!$request->isPost()) {
				$captcha->generate();
			} else if ($request->isPost() && !Core_Services_Captcha::isValid($captcha, $request)) {
				// The captcha is invalid
				$messenger->addMessage($this->view->translator()->_('global._share.captchaValidator'));
				$this->_redirect($loginUrl);
				exit;
			}
			$this->view->assign('captcha', $captcha);
		}
		
		$adapter = null;
		$sent	 = false;
		if ($request->isPost() && $request->getPost('openid_url', null) == null) {
			$username = $request->getPost('username');
			$password = $request->getPost('password');
			$adapter  = new Core_Services_Auth_Db($username, $password);
			$sent	  = true;
		} else if (($openIdEnabled && $request->isPost() && ($openIdUrl = $request->getPost('openid_url')))
			|| ($openIdEnabled && !$request->isPost() && 'id_res' == $request->getParam('openid_mode'))) 
		{
			$adapter = new Core_Services_Auth_OpenId($openIdUrl);
			$sent	 = true;
		}
		
		if ($sent) {
			$result   = $auth->authenticate($adapter);
			
			switch ($result->getCode()) {
				// Logged in successfully
				case Zend_Auth_Result::SUCCESS:
					$user = $auth->getIdentity();
					
					// Reset the login attempts
					$session = new Zend_Session_Namespace(self::SESSION_LOGIN_ATTEMPTS);
					reset($session->numLoginAttempts);
					
					Core_Base_Hook_Registry::getInstance()->executeAction('Core_Auth_Login_LoginSuccess', $user);
					
					// Redirect to the previous request if it is not Ajax request
					$session = new Zend_Session_Namespace(Core_Controllers_Plugins_UrlTracker::SESSION_NS);
					if (isset($session->url) && isset($session->isXhrRequest) && $session->isXhrRequest == false) {
						$this->_redirect($this->view->serverUrl() . $session->url);
					} else {
						$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index'));
					}
					break;
				
				// Found user, but the account has not been activated
				case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
				// Invalid OpenID URL, invalid username, or wrong password
				case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
				// User not found
				case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
				// General failure
				case Zend_Auth_Result::FAILURE:
					foreach ($result->getMessages() as $message) {
						$messenger->addMessage($this->view->translator()->_($message));
					}
					
					// Increase the login attempts
					$session = new Zend_Session_Namespace(self::SESSION_LOGIN_ATTEMPTS);
					$session->numLoginAttempts++;
					
					$this->_redirect($loginUrl);
					break;
			}
		}
		
		$this->view->assign('openIdEnabled', $openIdEnabled);
	}
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Logout
	 * 
	 * @return void
	 */
	public function logoutAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		$this->_helper->getHelper('layout')->disableLayout();
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect($this->view->baseUrl());
			exit();
		}
		$user = $auth->getIdentity();
		
		// Execute hook
		Core_Base_Hook_Registry::getInstance()->executeAction('Core_Auth_Logout_LogoutSuccess', $user);
		
		// Redirect to the previous request if it is not Ajax request
		$session = new Zend_Session_Namespace(Core_Controllers_Plugins_UrlTracker::SESSION_NS);
		if (isset($session->url) && isset($session->isXhrRequest) && $session->isXhrRequest == false) {
			$gotoUrl = $this->view->serverUrl() . $session->url;
		} else {
			$gotoUrl = $this->view->baseUrl();
		}
		
		// Clear session
		Zend_Session::destroy(false, false);
		$auth->clearIdentity();
		
		$this->_redirect($gotoUrl);
	}
}
