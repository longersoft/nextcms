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
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Auth_OpenId implements Zend_Auth_Adapter_Interface
{
	/**
	 * OpenId identifier
	 * 
	 * @var string
	 */
	private $_openIdUrl = null;
	
	/**
	 * @param string $openIdUrl
	 */
	public function __construct($openIdUrl)
	{
		$this->_openIdUrl = $openIdUrl;
	}
	
	/**
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate()
	{
		Core_Services_Db::connect('master');
		
		$storage   = new Core_Services_OpenIdStorage();
		$consumer  = new Core_Base_OpenId_Consumer($storage);
		$openIdUrl = $this->_openIdUrl;
		$user	   = null;
		$messages  = array();
		if (!empty($openIdUrl)) {
			if (!$consumer->login($openIdUrl)) {
				$result	  = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
				$messages = array('user._share.openIdUrlValidator');
			}
		} else {
			if ($consumer->verify($_GET, $openIdUrl)) {
				$user = Core_Services_User::getByOpenIdUrl($openIdUrl);
				switch (true) {
					// Not found user
					case (null == $user):
						$result   = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
						$messages = array('auth.login.notFoundUser');
						break;
					// User is not activated
					case ($user->status != Core_Models_User::STATUS_ACTIVATED):
						$result   = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
						$messages = array('auth.login.userNotActivated'); 
						break;
					// Success
					default:
						$result = Zend_Auth_Result::SUCCESS;
						// Define the role
						$user->role		 = Core_Services_Role::getById($user->role_id);
						$user->role_name =  $user->role->name;
						break;
				}
			} else {
				$result   = Zend_Auth_Result::FAILURE;
				$messages = array('auth.login.error'); 
			}
		}
		return new Zend_Auth_Result($result, $user, $messages);
	}
}
