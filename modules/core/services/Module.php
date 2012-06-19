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
 * @version		2012-03-02
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Module
{
	/**
	 * Gets array of bootstrap modules from the configuration file
	 * 
	 * @return array
	 */
	public static function getBootstrapModules()
	{
		$config = Core_Services_Config::getAppConfigs(true);
		if (isset($config['resources']['Core_Application_Resource_Modules']['modules'])
			&& $config['resources']['Core_Application_Resource_Modules']['modules']) 
		{
			$modules = $config['resources']['Core_Application_Resource_Modules']['modules'];
			return explode(',', $modules);
		}
		return array('core');
	}
	
	/**
	 * Gets the list of installed modules in order by the module's name
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getInstalledModules()
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core', 
									'name'   => 'Module',
								))
								->setDbConnection($conn)
								->getModules();
	}
	
	/**
	 * Installs module
	 * 
	 * @param string $module The module's name
	 * @return Core_Models_Module|null
	 */
	public static function install($module)
	{
		if ($module == null || empty($module) || !is_string($module)) {
			return null;
		}
		$module		  = strtolower($module);
		$conn		  = Core_Services_Db::getConnection();
		$moduleObject = Core_Services_Dao::factory(array(
											'module' => 'core', 
											'name'   => 'Module',
										 ))
										 ->setDbConnection($conn)
										 ->install($module);
		
		// Execute install callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'configs' . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['install']['callbacks'])) {
				foreach ($info['install']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		// Update the list of bootstrap modules
		$bootstrapModules = self::getBootstrapModules();
		
		if (!in_array($module, $bootstrapModules)) {
			$bootstrapModules[] = $module;
			sort($bootstrapModules);
			
			$config = Core_Services_Config::getAppConfigs();
			$config['resources']['Core_Application_Resource_Modules']['modules'] = implode(',', $bootstrapModules);
			Core_Services_Config::writeAppConfigs($config);
		}
		
		return $moduleObject;
	}
	
	/**
	 * Uninstalls module
	 * 
	 * @param string $module The module's name
	 * @return bool
	 */
	public static function uninstall($module)
	{
		// Don't allow to uninstall the "core" module
		if ($module == null || empty($module) || strtolower($module) == 'core') {
			return false;
		}
		$module = strtolower($module);
		$conn   = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core', 
							'name'   => 'Module',
						 ))
						 ->setDbConnection($conn)
						 ->uninstall($module);
		
		// Execute uninstall callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'configs' . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['uninstall']['callbacks'])) {
				foreach ($info['uninstall']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		// Update the list of bootstrap modules
		$bootstrapModules = self::getBootstrapModules();
		if (in_array($module, $bootstrapModules)) {
			$bootstrapModules = array_diff($bootstrapModules, array($module));
			sort($bootstrapModules);
			
			$config = Core_Services_Config::getAppConfigs();
			$config['resources']['Core_Application_Resource_Modules']['modules'] = implode(',', $bootstrapModules);
			Core_Services_Config::writeAppConfigs($config);
		}
		
		return true;
	}
}
