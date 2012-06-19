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
 * @version		2012-02-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_RegistrationController extends Zend_Controller_Action
{
	const SESSION_OPENID_URL = 'Core_RegistrationController_OpenId';
	
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Activates account
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$key	 = $request->getParam('activation_key');
		$users	 = Core_Services_User::find(array(
			'activation_key' => $key,
		));
		if ($users == null || count($users) == 0) {
			$this->view->assign('message', $this->view->translator()->_('registration.activate.notFoundActivationKey'));
			return;
		}
		$user = $users[0];
		if (md5($user->user_id . '_' . $user->user_name . '_' . $user->email) != $key) {
			$this->view->assign('message', $this->view->translator()->_('registration.activate.invalidActivationKey'));
			return;
		}
		
		// The activation key is valid, activate the user
		$user->status = Core_Models_User::STATUS_NOT_ACTIVATED;
		Core_Services_User::toggleActiveStatus($user);
		
		// Reset the activation key
		$user->activation_key = null;
		Core_Services_User::update($user);
		
		$loginUrl = $this->view->serverUrl() . $this->view->url(array(), 'core_auth_login');
		$this->view->assign('message', stripslashes(sprintf($this->view->translator()->_('registration.activate.success'), $loginUrl)));
	}
	
	/**
	 * Registers new account
	 * 
	 *  @return void
	 */
	public function registerAction()
	{
		Core_Services_Db::connect('master');
		
		$messenger		 = $this->_helper->getHelper('FlashMessenger');
		$registerEnabled = Core_Services_Config::get('core', 'register_enabled', 'true') == 'true';
		$openIdEnabled   = Core_Services_Config::get('core', 'register_openid_enabled', 'false') == 'true';
		$this->view->assign(array(
			'registerEnabled' => $registerEnabled,
			'openIdEnabled'	  => $openIdEnabled,
		));
		if (!$registerEnabled) {
			return;
		}
		
		$request   = $this->getRequest();
		$step	   = $request->getParam('step', 'register');
		$openIdUrl = $request->getParam('openid_url');
		$returnUrl = $this->view->serverUrl() . $this->view->url(array(), 'core_registration_register');
		
		// Check if the OpenId URL is already used or not
		if ($openIdUrl && ($users = Core_Services_User::getByOpenIdUrl($openIdUrl)) && count($users) > 0) {
			$messenger->addMessage($this->view->translator()->_('user._share.openIdUrlInUse'));
			$this->_redirect($returnUrl);
			return;
		}
		
		$captcha = Core_Services_Captcha::getCaptcha();
		if (!$request->isPost()) {
			$captcha->generate();
		} else if ($request->isPost() && !Core_Services_Captcha::isValid($captcha, $request)) {
			// The captcha is invalid
			$messenger->addMessage($this->view->translator()->_('global._share.captchaValidator'));
			$this->_redirect($returnUrl);
			exit;
		}
				
		switch ($step) {
			case 'verify':
				// Verify OpenId URL
				$storage		= new Core_Services_OpenIdStorage();
				$consumer		= new Core_Base_OpenId_Consumer($storage);
				$axExtension 	= new Core_Base_OpenId_Extension_Ax(array(
										'firstname' => true,
										'email'		=> true,
										'lastname'	=> true,
										'dob'		=> true,
										'gender'	=> true,
										'postcode'	=> true,
										'country'	=> true,
										'language'	=> true,
										'timezone'	=> true,
									), null, 1.1);
				$sregExtension = new Zend_OpenId_Extension_Sreg(array(
										'firstname' => true,
										'email'		=> true,
										'lastname'	=> true,
										'dob'		=> true,
										'gender'	=> true,
										'postcode'	=> true,
										'country'	=> true,
										'language'	=> true,
										'timezone'	=> true,
									), null, 1.1);
				// FIXME: Remove the extension variables, because I don't need 
				// to get the personal information anymore
				$openIdMode = $request->getParam('openid_mode');
				if ($openIdUrl) {
					$extension = preg_match('/profiles.google.com\//i', $openIdUrl, $r) ? $axExtension : $sregExtension;
					if (!$consumer->login($openIdUrl, $returnUrl . '?step=verify')) {
						// Invalid OpenID URL
						$messenger->addMessage($this->view->translator()->_('user._share.openIdUrlValidator'));
						$this->_redirect($returnUrl);
					}
				} elseif (isset($openIdMode) && 'id_res' == $openIdMode) {
					$extension = preg_match('/profiles.google.com\//i', $request->getParam('openid_identity'), $r) ? $axExtension : $sregExtension;
					if ($consumer->verify($_GET, $id)) {
						// Store the Id in the session
						$session = new Zend_Session_Namespace(self::SESSION_OPENID_URL);
						$session->id   = $id;
						$session->pros = serialize($extension->getProperties());
						$this->_redirect($returnUrl);
					}
				}
				break;
			case 'register':
				// Register new account
				$session = new Zend_Session_Namespace(self::SESSION_OPENID_URL);
				if ($session->id) {
					$this->view->assign('openIdUrl', $session->id);
				}
				
				if ($request->isPost()) {
					// Check if the auto-activate registration is enabled or not
					$autoActivate = Core_Services_Config::get('core', 'register_auto_activate', 'false') == 'true';
					$defaultRole  = Core_Services_Config::get('core', 'register_default_role');
					
					// Add new user
					$user = new Core_Models_User(array(
						'user_name'	   => $request->getPost('user_name'),
						'email'		   => $request->getPost('email'),
						'password'	   => $request->getPost('password'),
						'role_id'	   => $defaultRole ? $defaultRole : -1,
						'created_date' => date('Y-m-d H:i:s'),
						'status'	   => $autoActivate ? Core_Models_User::STATUS_ACTIVATED : Core_Models_User::STATUS_NOT_ACTIVATED,
					));
					
					$userId = Core_Services_User::add($user);
					$user->user_id = $userId;
					$result = ($userId === false) ? false : true;
					
					$messenger->addMessage($this->view->translator()->_($result ? 'registration.register.success' : 'registration.register.error'));
					
					// Map OpenId URL with the user
					if ($openIdEnabled && $result && $openIdUrl) {
						Core_Services_User::addOpenIdAssoc($user, $openIdUrl);
						// Unset the session
						unset($session->id);
						unset($session->pros);
					}
					
					if ($result && !$autoActivate && Core_Services_Config::get('core', 'register_email_activate', 'false') == 'true') {
						// Generate activation key
						$user->activation_key = md5($user->user_id . '_' . $user->user_name . '_' . $user->email);
						Core_Services_User::update($user);
						
						$template = Core_Services_Mail::getBuiltinTemplate('core', 'activating_account_template');
						$search   = array('###username###', '###link###');
						$replace  = array($user->user_name, $this->view->serverUrl() . $this->view->url(array('activation_key' => $user->activation_key), 'core_registration_activate'));
						$subject  = str_replace($search, $replace, $template['subject']);
						$content  = str_replace($search, $replace, $template['content']);
						
						// Send activation email to user
						try {
							$transport = Core_Services_Mail::getMailTransport();
							$mail	   = new Zend_Mail();
							$mail->setFrom($template['from_email'], $template['from_name'])					
								 ->addTo($user->email, $user->user_name)
								 ->setSubject($subject)
								 ->setBodyHtml($content)
								 ->send($transport);
							$messenger->addMessage($this->view->translator()->_('registration.register.activationEmailSentSuccess'));
						} catch (Exception $ex) {
							$messenger->addMessage($this->view->translator()->_('registration.register.activationEmailSentError'));
						}
					}
					
					// Redirect
					$this->_redirect($returnUrl);
				}
				break;
		}
		
		$this->view->assign('captcha', $captcha);
	}
}
