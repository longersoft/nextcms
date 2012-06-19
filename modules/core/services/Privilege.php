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

class Core_Services_Privilege
{
	/**
	 * Adds new privilege
	 * 
	 * @param Core_Models_Privilege $privilege
	 * @return string The id of new privilege
	 */
	public static function add($privilege)
	{
		if ($privilege == null || !($privilege instanceof Core_Models_Privilege)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Privilege',
								))
								->setDbConnection($conn)
								->add($privilege);
	}
	
	/**
	 * Lists the privileges of given role to a resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param Core_Models_Role $role
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getByRole($resource, $role)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Privilege',
								))
								->setDbConnection($conn)
								->getByRole($resource, $role);
	}
	
	/**
	 * Lists the privileges of given user to a resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param Core_Models_User $user
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getByUser($resource, $user)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Privilege',
								))
								->setDbConnection($conn)
								->getByUser($resource, $user);
	}
	
	/**
	 * Gets resources and privileges of given module
	 * 
	 * @param string $module Name of module
	 * @return array
	 */
	public static function getPrivileges($module)
	{
		$privileges = array(
			'module' => array(),
		);
		if ($items = self::getExtensionPrivileges($module)) {
			$privileges['module'] = $items;
		}
		
		foreach (array('hook', 'plugin', 'task', 'widget') as $type) {
			$extPrivileges = array();
			
			// Get the list of extensions
			$dirs = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . $type . 's');
			foreach ($dirs as $dir) {
				if ($items = self::getExtensionPrivileges($module, $dir, $type)) {
					$extPrivileges = array_merge($extPrivileges, $items);
				}
			}
			
			$privileges[$type] = $extPrivileges;
		}
		return $privileges;
	}
	
	/**
	 * Gets resources and privileges of given extension
	 * 
	 * @param string $module The module's name
	 * @param string $name The extension's name. It is NULL if you want to get the privileges of module
	 * @param string $extensionType The extension's type. Can be "module", "hook", "plugin", "task", "widget"
	 * @return array An array which each item consists of two members:
	 * - resource: Represent a resource
	 * - privileges: Array of privileges associating with the resource 
	 */
	public static function getExtensionPrivileges($module, $name = null, $extensionType = 'module')
	{
		$file		 = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS;
		$languageDir = 'modules/' . $module;
		switch ($extensionType) {
			case 'module':
				$file		 .= 'configs' . DS . 'permissions.php';
				$languageDir .= '/languages';
				break;
			case 'hook':
			case 'plugin':
			case 'task':
			case 'widget':
				$dir		  = $extensionType . 's';
				$file		 .= $dir . DS . $name . DS . 'permissions.php';
				$languageDir .= '/' . $dir . '/' . $name;
				break;
		}		
		if (!file_exists($file)) {
			return null;
		}
		$permissions = include_once $file;
		if (!is_array($permissions)) {
			return null;
		}
		
		$return = array();
		$view	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		foreach ($permissions as $controller => $info) {
			$resource   = new Core_Models_Resource(array(
											'parent_id' 	  => null,
											'description' 	  => $view->translator()->setLanguageDir($languageDir)->_($info['translationKey'], $info['description']),
											'module_name' 	  => $module,
											'controller_name' => $controller,
											'extension_type'  => $extensionType,
										));
			$privileges = array();
			if (isset($info['actions']) && is_array($info['actions'])) {
				foreach ($info['actions'] as $action => $details) {
					$privileges[] = new Core_Models_Privilege(array(
													'description' 	  => $view->translator()->setLanguageDir($languageDir)->_($details['translationKey'], $details['description']),
													'module_name' 	  => $module,
													'controller_name' => $controller,
													'action_name' 	  => $action,
													'extension_type'  => $extensionType,
												));
				}
			}
			
			$return[] = array(
				'resource'   => $resource,
				'privileges' => $privileges,
			);
		}
		
		// Reset the language dir
		$view->translator()->setLanguageDir(null);
		
		return $return;
	}
	
	/**
	 * Adds resources, privileges of all modules.
	 * It also removes no longer in use privileges.
	 * 
	 * @param array $modules Array of modules' names
	 * @return bool
	 */
	public static function sync($modules)
	{
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'	 => 'Privilege',
						 ))
						 ->setDbConnection($conn)
						 ->sync($modules);
		return true;
	}
}
