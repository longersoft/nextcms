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
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Widget
{
	/**
	 * Finds installed widgets
	 * 
	 * @param array $criteria Contains the following keys:
	 * - module: The name of module that widgets belong to
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core', 
									'name'   => 'Widget',
								))
								->setDbConnection($conn)
								->find($criteria);
	}
	
	/**
	 * Gets a widget instance from given name and its module
	 * 
	 * @param string $name Name of widget
	 * @param string $module Name of module
	 * @return Core_Models_Widget|null
	 */
	public static function getWidgetInstance($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $name . DS . 'about.php';
		if (!file_exists($file)) {
			return null;
		}
		$info = include $file;
		if (!is_array($info)) {
			return null;
		}
		return new Core_Models_Widget(array(
			'module'	  => $module,
			'name'		  => $name,
			'title'		  => $info['title']['description'],
			'description' => $info['description']['description'],
			'thumbnail'	  => $info['thumbnail'],
			'website'	  => $info['website'],
			'author'	  => $info['author'],
			'email'		  => $info['email'],
			'version'	  => $info['version'],
			'app_version' => $info['appVersion'],
			'license'	  => $info['license'],
		));
	}

	/**
	 * Installs a widget
	 * 
	 * @param string $name Name of widget
	 * @param string $module Name of module
	 * @return bool
	 */
	public static function install($name, $module)
	{
		$name	  = strtolower($name);
		$module	  = strtolower($module);	
		$conn	  = Core_Services_Db::getConnection();
		$widgetId = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Widget',
									 ))
									 ->setDbConnection($conn)
									 ->install($name, $module);
		if ($widgetId === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Widgets_' . ucfirst($name) . '_Widget';
		if (class_exists($class)) {
			$widget = new $class();
			if ($widget instanceof Core_Base_Extension_Widget) {
				$widget->install();
			}
		}
		
		// Execute install callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $name . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['install']['callbacks'])) {
				foreach ($info['install']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Uninstalls a widget
	 * 
	 * @param string $name Name of widget
	 * @param string $module Name of module
	 * @return bool
	 */
	public static function uninstall($name, $module)
	{
		$name	= strtolower($name);
		$module = strtolower($module);
		$conn	= Core_Services_Db::getConnection();
		$result = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Widget',
								   ))
								   ->setDbConnection($conn)
								   ->uninstall($name, $module);
		if ($result === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Widgets_' . ucfirst($name) . '_Widget';
		if (class_exists($class)) {
			$widget = new $class();
			if ($widget instanceof Core_Base_Extension_Widget) {
				$widget->uninstall();
			}
		}
		
		// Execute uninstall callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $name . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['uninstall']['callbacks'])) {
				foreach ($info['uninstall']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		return true;
	}
}
