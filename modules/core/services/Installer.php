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
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Installs the app including install available modules, init privileges, etc
 */
class Core_Services_Installer
{
	/**
	 * This will be called at the last step when installing the app. It creates root user, root role, and 
	 * provides the full permissions to the user.
	 * 
	 * @param string $password The root user's password
	 * @param string $email The root user's email
	 * @return bool
	 */
	public static function addRootUser($password, $email)
	{
		if (!$password || !$email) {
			return false;
		}
		
		Core_Services_Db::connect('master');
		
		// Add root role
		$role = new Core_Models_Role(array(
			'role_id' 	  => null,
			'name' 		  => Core_Models_Role::ROOT_ROLE,
			'description' => 'Admin',
			'locked' 	  => 1,
			'num_users'   => 0,
		));
		$roleId = Core_Services_Role::add($role);
		
		// Add root user
		$user = new Core_Models_User(array(
			'user_name'    => Core_Models_User::ROOT_USER,
			'password' 	   => $password,
			'email' 	   => $email,
			'status'	   => Core_Models_User::STATUS_ACTIVATED,
			'created_date' => date('Y-m-d H:i:s'),
			'role_id'	   => $roleId,
			'role_name'    => Core_Models_Role::ROOT_ROLE,
		));
		Core_Services_User::add($user);
		
		// Set the full privileges
		$rule = new Core_Models_Rule(array(
			'obj_id' 		  => $roleId,
			'obj_type' 		  => Core_Models_Rule::TYPE_ROLE,
			'allow' 		  => 1,
			'action_name' 	  => null,
			'resource_name'   => null,
			'module_name'     => null,
			'controller_name' => null,
			'extension_type'  => null,
		));
		Core_Services_Rule::add($rule);
		
		return true;
	}
	
	/**
	 * This method is called when installing app using Install Wizard
	 * 
	 * @return void
	 */
	public static function installApp()
	{
		// Unregistry all objects
		Zend_Registry::_unsetInstance();
		
		Core_Services_Db::connect('master');
		
		// Install other modules
		$modules = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
		foreach ($modules as $module) {
			if ($module != 'core') {
				Core_Services_Module::install($module);
			}
		}
		
		// Generate a secret key to encrypt, decrypt data
		require_once 'PasswordHash.php';
		$secretKey = Core_Base_String::generateRandomString();
		$hasher    = new PasswordHash(8, true);
		$secretKey = $hasher->HashPassword($secretKey);
		Core_Services_Config::set('core', 'secret_key', $secretKey);
	}
	
	/**
	 * The callback that is called after installing the module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		Core_Services_Hook::install('layout', 'core');
		Core_Services_Hook::install('userprovider', 'core');
	}	
}
