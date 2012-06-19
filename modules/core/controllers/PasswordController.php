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
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_PasswordController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////

	/**
	 * Generates password, secret key
	 * 
	 * @return void
	 */
	public function generateAction()
	{
		$request = $this->getRequest();
		$hash    = $request->getParam('hash', 'false');
		$length  = $request->getParam('length', 8);
		
		$password = Core_Base_String::generateRandomString($length);
		if ($hash == 'true') {
			require_once 'PasswordHash.php';
			$hasher   = new PasswordHash(8, true);
			$password = $hasher->HashPassword($password);
		}
		
		$this->_helper->json(array(
			'password' => $password,
		));
	}
	
	/**
	 * Sends new password
	 * 
	 * @return void
	 */
	public function sendAction()
	{
		$request	 = $this->getRequest();
		$redirectUrl = $this->view->serverUrl() . $this->view->url(array(), 'core_password_send');
		
		// If user already logged in, redirect to the dashboard
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index'));
			exit();
		}
		
		$this->view->assign('sent', null);
		if ($request->isPost()) {
			$username  = $request->getPost('user_name');
			$email     = $request->getPost('email');
			$messenger = $this->_helper->getHelper('FlashMessenger');
			
			switch (true) {
				case ($username == null || $username == ''):
					$messenger->addMessage($this->view->translator()->_('user._share.usernameRequired'));
					$this->_redirect($redirectUrl);
					break;
				case ($email == null || $email == ''):
					$messenger->addMessage($this->view->translator()->_('user._share.emailRequired'));
					$this->_redirect($redirectUrl);
					break;
				default:
					Core_Services_Db::connect('master');
					$users = Core_Services_User::find(array(
						'user_name' => $username,
						'email'		=> $email,
					));
					if (count($users) == 0) {
						$messenger->addMessage($this->view->translator()->_('password.send.notFoundUser'));
						$this->_redirect($redirectUrl);
					} else {
						$user = $users[0];
						
						// Send the resetting password URL to user's email
						$template = Core_Services_Mail::getBuiltinTemplate('core', 'sending_password_template');
						
						// Generate the password and update the user's password
						$password		= Core_Services_User::generatePassword(16);
						$user->password = $password;
						Core_Services_User::update($user);
						
						// Replace the macros
						$search  = array('###username###', '###email###', '###password###', '###link###');
						$replace = array($user->user_name, $user->email, $password, $this->view->serverUrl() . $this->view->url(array(), 'core_auth_login'));
						$subject = str_replace($search, $replace, $template['subject']);
						$content = str_replace($search, $replace, $template['content']);
						
						// Send email to user
						try {
							$transport = Core_Services_Mail::getMailTransport();
							$mail	   = new Zend_Mail();
							$mail->setFrom($template['from_email'], $template['from_name'])					
								 ->addTo($user->email, $user->user_name)
								 ->setSubject($subject)
								 ->setBodyHtml($content)
								 ->send($transport);
							
							$this->view->assign('sent', true);
						} catch (Exception $ex) {
							$this->view->assign('sent', false);							
						}
					}
					break;
			}
		}
	}
}
