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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Plugin
{
	/**
	 * Enables/disables a plugin
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @param bool $enable If TRUE, enables the plugin. If FALSE, disable the plugin
	 * @return bool
	 */
	public static function enable($name, $module, $enable = true)
	{
		if (!$name || !$module) {
			throw new Exception('The name and module of plugin are required');
		}
		
		$name	= strtolower($name);
		$module	= strtolower($module);
		$conn	= Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core', 
							'name'   => 'Plugin',
						 ))
						 ->setDbConnection($conn)
						 ->enable($name, $module, $enable);
		return true;
	}
	
	/**
	 * Gets list of installed plugins
	 * 
	 * @param bool $enabled If TRUE, returns the enabled plugins
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getInstalledPlugins($enabled = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core', 
									'name'   => 'Plugin',
								))
								->setDbConnection($conn)
								->getPlugins($enabled);
	}
	
	/**
	 * Gets plugin's options
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @param array $defaultOptions The default options
	 * @return array|null
	 */
	public static function getOptions($name, $module, $defaultOptions = array())
	{
		$conn	 = Core_Services_Db::getConnection();
		$options = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Plugin',
									))
									->setDbConnection($conn)
									->getOptions($name, $module);
		if ($options == null || empty($options)) {
			return $defaultOptions;
		}
		return Zend_Json::decode($options);
	}
	
	/**
	 * Gets plugin's options
	 * 
	 * @param Core_Base_Controllers_Plugin $plugin The plugin instance
	 * @param array $defaultOptions The default options
	 * @return array
	 */
	public static function getOptionsByInstance($plugin, $defaultOptions = array())
	{
		if (!($plugin instanceof Core_Base_Controllers_Plugin)) {
			throw new Exception('The param is not an instance of Core_Base_Controllers_Plugin');
		}
		return self::getOptions($plugin->getName(), $plugin->getModule(), $defaultOptions);
	}
	
	/**
	 * Gets a plugin instance from given name and its module
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @return Core_Models_Plugin|null
	 */
	public static function getPluginInstance($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'plugins' . DS . $name . DS . 'about.php';
		if (!file_exists($file)) {
			return null;
		}
		$info = include $file;
		if (!is_array($info)) {
			return null;
		}
		return new Core_Models_Plugin(array(
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
			'options'	  => isset($info['options']) ? $info['options'] : null,
		));
	}

	/**
	 * Installs a plugin
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @return bool
	 */
	public static function install($name, $module)
	{
		$name	  = strtolower($name);
		$module	  = strtolower($module);
		$conn	  = Core_Services_Db::getConnection();
		$pluginId = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Plugin',
									 ))
									 ->setDbConnection($conn)
									 ->install($name, $module);
		if ($pluginId === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Plugins_' . ucfirst($name) . '_Plugin';
		if (class_exists($class)) {
			$plugin = new $class();
			if ($plugin instanceof Core_Base_Controllers_Plugin) {
				$plugin->install();
			}
		}
		
		// Execute install callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'plugins' . DS . $name . DS . 'about.php';
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
	 * Updates plugin's options
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @param array $options
	 * @return bool
	 */
	public static function setOptions($name, $module, $options)
	{
		if (!$name || !$module) {
			return false;
		}
		$options = ($options == null || !is_array($options)) ? null : Zend_Json::encode($options);  
		$conn	 = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core', 
							'name'   => 'Plugin',
						 ))
						 ->setDbConnection($conn)
						 ->setOptions($name, $module, $options);
		return true;
	}
	
	/**
	 * Sets options to given instance of plugin
	 * 
	 * @param Core_Base_Controllers_Plugin $plugin The plugin instance
	 * @param array $options
	 * @return bool
	 */
	public static function setOptionsForInstance($plugin, $options)
	{
		if (!($plugin instanceof Core_Base_Controllers_Plugin)) {
			throw new Exception('The param is not an instance of Core_Base_Controllers_Plugin');
		}
		return self::setOptions($plugin->getName(), $plugin->getModule(), $options);
	}
	
	/**
	 * Uninstalls a plugin
	 * 
	 * @param string $name Name of plugin
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
										'name'   => 'Plugin',
								   ))
								   ->setDbConnection($conn)
								   ->uninstall($name, $module);
		if ($result === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Plugins_' . ucfirst($name) . '_Plugin';
		if (class_exists($class)) {
			$plugin = new $class();
			if ($plugin instanceof Core_Base_Controllers_Plugin) {
				$plugin->uninstall();
			}
		}
		
		// Execute uninstall callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'plugins' . DS . $name . DS . 'about.php';
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
