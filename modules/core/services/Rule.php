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
 * @version		2011-10-31
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Rule
{
	/**
	 * Adds new rule
	 * 
	 * @param Core_Models_Rule $rule Rule instance
	 * @return string The id of newly created rule
	 */
	public static function add($rule)
	{
		if (!$rule || !($rule instanceof Core_Models_Rule)) {
			throw new Exception('The first param is not an instance of Core_Models_Rule');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Rule',
								))
						  	  	->setDbConnection($conn)
						  	  	->add($rule);
	}
	
	/**
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getAclRules()
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Rule',
								))
								->setDbConnection($conn)
								->getAclRules();
	}
	
	/**
	 * Checks if the current logged in user has the permission to do given action or not
	 * 
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param string $extensionType
	 * @param string $callback
	 * @param array $params
	 */
	public static function isAllowed($action, $controller = null, $module = null, $extensionType = 'module', $callback = null, $params = null) 
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			return false;
		}

		$action = strtolower($action);

		// Get module and controller name
		if (null == $controller) {
			$controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		}
		if (null == $module) {
			$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		}

		$isAllowed = Core_Services_Acl::getInstance()->isUserOrRoleAllowed($module, $controller, $action, $extensionType);
		if (!$isAllowed) {
			return false;
		}
		if (null != $callback) {
			if (false !== ($pos = strpos($callback, '::'))) {
				$callback = array(substr($callback, 0, $pos), substr($callback, $pos + 2));
			}
			return call_user_func_array($callback, $params);
		}

		return true;
	}
	
	/**
	 * Sets role permissions
	 * 
	 * @param Core_Models_Role $role The role instance
	 * @param array $privileges Array of privileges. Each item is a string defined as actioName_controllerName_moduleName
	 * @return bool
	 */
	public static function setRolePermissions($role, $privileges)
	{
		if (!$role || !($role instanceof Core_Models_Role)) {
			throw new Exception('The first param is not an instance of Core_Models_Role');
		}
		
		if ($role->locked || $role->isRootRole()) {
			return false;
		}
		
		// Set role's permissions
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'	 => 'Rule',
						 ))
						 ->setDbConnection($conn)
						 ->setRolePermissions($role, $privileges);
		return true;
	}
	
	/**
	 * Sets user permissions
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param array $privileges Array of privileges. Each item is a string defined as actioName_controllerName_moduleName
	 * @return bool
	 */
	public static function setUserPermissions($user, $privileges)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The first param is not an instance of Core_Models_User');
		}
		if ($user->isRootUser()) {
			return false;
		}
		
		// Set user's permissions
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'	 => 'Rule',
						 ))
						 ->setDbConnection($conn)
						 ->setUserPermissions($user, $privileges);
		return true;
	}
}
