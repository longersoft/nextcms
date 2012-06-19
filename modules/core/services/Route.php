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
 * @version		2011-12-13
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Route
{
	/**
	 * Loads all routes configured in each modules
	 * 
	 * @return void
	 */
	public static function loadRoutes()
	{
		Core_Services_Db::connect('master');
		
		// Allow user to change the default prefix of all the back-end URLs
		// from admin to anything else.
		// The prefix is stored in the database settings under the key "admin_prefix".
		// Below is example of route configuration:
		// 		$routes['core_user_list']['type']  = "Zend_Controller_Router_Route_Static";
		//		$routes['core_user_list']['route'] = "{adminPrefix}/core/user/list";
		$adminPrefix = Core_Services_Config::get('core', 'admin_prefix', 'admin');
		
		$modules	 = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
		foreach ($modules as $module) {
			$configFiles = self::_loadModuleRoutes($module);
			foreach ($configFiles as $file) {
				self::loadRoutesFromFile($file, $adminPrefix);
			}
		}
	}
	
	/**
	 * Loads routes from given file
	 * 
	 * @param string $file The file's path
	 * @param string $adminPrefix Admin prefix URL
	 * @return void
	 */
	public static function loadRoutesFromFile($file, $adminPrefix)
	{
		if (!file_exists($file)) {
			return;
		}
		$config = include $file;
		$router = Zend_Controller_Front::getInstance()->getRouter();
		foreach ($config as $name => $settings) {
			$settings['route'] = str_replace('{adminPrefix}', $adminPrefix, $settings['route']);
			if (isset($settings['reverse'])) {
				$settings['reverse'] = str_replace('{adminPrefix}', $adminPrefix, $settings['reverse']);
			}
			$router->addConfig(new Zend_Config(array($name => $settings)));
		}
	}
	
	/**
	 * Loads all route configuration files in given module
	 * 
	 * @param string $module The module name
	 * @return array
	 */
	private static function _loadModuleRoutes($module)
	{
		$dir = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'configs' . DS . 'routes';
		if (!is_dir($dir)) {
			return array();
		}
		
		$configFiles = array();
		$dirIterator = new DirectoryIterator($dir);
		
		foreach ($dirIterator as $file) {
			if ($file->isDot() || $file->isDir()) {
				continue;
			}
			// Check the extension of file (accept PHP file only)
			$name     = $file->getFilename();
			$pathInfo = pathinfo($name);
			if ($pathInfo['extension'] == 'php') {
				$configFiles[] = $dir . DS . $name;
			}
		}
		
		return $configFiles;
	}
}
