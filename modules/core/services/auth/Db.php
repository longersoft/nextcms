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
 * @version		2011-10-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Auth_Db implements Zend_Auth_Adapter_Interface 
{
	/**
	 * The username
	 * 
	 * @var string
	 */
	private $_username;
	
	/**
	 * The password
	 * 
	 * @var string
	 */
	private $_password;
	
	/**
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username, $password) 
	{
		$this->_username = $username;
		$this->_password = $password;
	}

	/**
	 * Performs an authentication attempt
	 * 
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		Core_Services_Db::connect('master');
		
		// Get user by username
		$users = Core_Services_User::find(array(
			'user_name' => $this->_username,
		));
		if ($users == null || count($users) != 1) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array('auth.login.notFoundUser'));
		}
		
		$user = $users[0];
		
		// Check if the password is match or not
		$password = $user->password;
		
		require_once 'PasswordHash.php';
		$hasher		   = new PasswordHash(8, true);
		$passwordMatch = $hasher->CheckPassword($this->_password, $password);
		
		if (!$passwordMatch) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, array('auth.login.wrongPassword'));
		}
		
		// The password matches, check if the user is activated or not
		if ($user->status != Core_Models_User::STATUS_ACTIVATED) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS, null, array('auth.login.userNotActivated'));
		}
		
		// Define the role
		$user->role		 = Core_Services_Role::getById($user->role_id);
		$user->role_name = $user->role->name;
		
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
	}
}
