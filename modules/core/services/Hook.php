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
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Hook
{
	/**
	 * Gets list of installed hooks
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getInstalledHooks($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core', 
									'name'   => 'Hook',
								))
								->setDbConnection($conn)
								->getHooks($criteria);
	}
	
	/**
	 * Gets hook's options
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @return array|null
	 */
	public static function getOptions($name, $module)
	{
		$conn	 = Core_Services_Db::getConnection();
		$options = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Hook',
									))
									->setDbConnection($conn)
									->getOptions($name, $module);
		if ($options == null || empty($options)) {
			return null;
		}
		return Zend_Json::decode($options);
	}
	
	/**
	 * Gets hook's options
	 * 
	 * @param Core_Base_Extension_Hook $hook The hook instance
	 * @return array
	 */
	public static function getOptionsByInstance($hook)
	{
		if (!($hook instanceof Core_Base_Extension_Hook)) {
			throw new Exception('The param is not an instance of Core_Base_Extension_Hook');
		}
		return self::getOptions($hook->getName(), $hook->getModule());
	}
	
	/**
	 * Gets a hook instance from given name and its module
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @return Core_Models_Hook|null
	 */
	public static function getHookInstance($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'hooks' . DS . $name . DS . 'about.php';
		if (!file_exists($file)) {
			return null;
		}
		$info = include $file;
		if (!is_array($info)) {
			return null;
		}
		return new Core_Models_Hook(array(
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
			'options'	  => $info['options'],
		));
	}
	
	/**
	 * Installs a hook
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @return bool
	 */
	public static function install($name, $module)
	{
		$name	= strtolower($name);
		$module	= strtolower($module);	
		$conn	= Core_Services_Db::getConnection();
		$hookId = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Hook',
								   ))
								   ->setDbConnection($conn)
								   ->install($name, $module);
		if ($hookId === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Hooks_' . ucfirst($name) . '_Hook';
		if (class_exists($class)) {
			$hook = new $class();
			if ($hook instanceof Core_Base_Extension_Hook) {
				$hook->install();
			}
		}
		
		// Execute install callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'hooks' . DS . $name . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['install']['callbacks'])) {
				foreach ($info['install']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		// Execute hook
		Core_Base_Hook_Registry::getInstance()->executeAction('Core_Services_Hook_Install_Success_' . $module . $name);
		
		return true;
	}
	
	/**
	 * Updates hook's options
	 * 
	 * @param string $name Name of hook
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
							'name'   => 'Hook',
						 ))
						 ->setDbConnection($conn)
						 ->setOptions($name, $module, $options);
		return true;
	}
	
	/**
	 * Sets options to given instance of hook
	 * 
	 * @param Core_Base_Extension_Hook $hook The hook instance
	 * @param array $options
	 * @return bool
	 */
	public static function setOptionsForInstance($hook, $options)
	{
		if (!($hook instanceof Core_Base_Extension_Hook)) {
			throw new Exception('The param is not an instance of Core_Base_Extension_Hook');
		}
		return self::setOptions($hook->getName(), $hook->getModule(), $options);
	}
	
	/**
	 * Uninstalls a hook
	 * 
	 * @param string $name Name of hook
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
										'name'   => 'Hook',
								   ))
								   ->setDbConnection($conn)
								   ->uninstall($name, $module);
		if ($result === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Hooks_' . ucfirst($name) . '_Hook';
		if (class_exists($class)) {
			$hook = new $class();
			if ($hook instanceof Core_Base_Extension_Hook) {
				$hook->uninstall();
			}
		}
		
		// Execute uninstall callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'hooks' . DS . $name . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['uninstall']['callbacks'])) {
				foreach ($info['uninstall']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		// Execute hook
		Core_Base_Hook_Registry::getInstance()->executeAction('Core_Services_Hook_Uninstall_Success_' . $module . $name);
		
		return true;
	}
	
	/**
	 * Array of hook instances on page when it is loaded.
	 * There is only one instance for a given hook class
	 * 
	 * @var array
	 */
	private static $_hookInstances = array();
	
	/**
	 * Registers a hook instance for given class
	 * 
	 * @param string $hookClass The hook class
	 * @param Core_Base_Extension_Hook $hook The hook instance
	 * @throws Exception
	 * @return void
	 */
	public static function registerInstance($hookClass, $hook)
	{
		if (!($hook instanceof Core_Base_Extension_Hook)) {
			throw new Exception('The param is not an instance of Core_Base_Extension_Hook');
		}
		self::$_hookInstances[$hookClass] = $hook;
	}
	
	/**
	 * Gets hook instance which is registered by given class
	 *  
	 * @param string $hookClass The hook class. If this param is not passed,
	 * the method returns all registered instances
	 * @return Core_Base_Extension_Hook
	 */
	public static function getRegisteredInstance($hookClass = null)
	{
		return $hookClass 
			   ? (isset(self::$_hookInstances[$hookClass]) ? self::$_hookInstances[$hookClass] : null)
			   : self::$_hookInstances;
	}
}
