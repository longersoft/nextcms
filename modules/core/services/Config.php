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

class Core_Services_Config
{
	/**
	 * @var const
	 */
	const KEY_APP_CONFIG = 'Core_Services_Config_getAppConfigs';
	
	/**
	 * Gets the application configs from file
	 * 
	 * @param bool $refresh If TRUE, the application will read settings from the config file.
	 * Otherwise, it will take data from Zend_Registry
	 * @return array
	 */
	public static function getAppConfigs($refresh = false)
	{
		$hostName = $_SERVER['SERVER_NAME'];
		$hostName = (substr($hostName, 0, 3) == 'www') ? substr($hostName, 4) : $hostName;
		$key      = self::KEY_APP_CONFIG . $hostName;
		
		if ($refresh || !Zend_Registry::isRegistered($key)) {
			// Defines the config file based on the application environment
			$default = APP_ROOT_DIR . DS . 'configs' . DS . 'application.' . strtolower(APP_ENV) . '.php';
			$host    = APP_ROOT_DIR . DS . 'configs' . DS . $hostName . '.' . strtolower(APP_ENV) . '.php';
			$file 	 = file_exists($host) ? $host : $default;
			$config  = include $file;
			if (!is_array($config)) {
				throw new Exception('The configuration file does not return array');
			}
			Zend_Registry::set($key, $config);
			return $config;
		}

		return Zend_Registry::get($key);
	}
	
	/**
	 * Writes configuration data to file
	 * 
	 * @param array $config
	 * @return void
	 */
	public static function writeAppConfigs($config)
	{
		$hostName = $_SERVER['SERVER_NAME'];
		$hostName = (substr($hostName, 0, 3) == 'www') ? substr($hostName, 4) : $hostName;
		$default  = APP_ROOT_DIR . DS . 'configs' . DS . 'application.' . strtolower(APP_ENV) . '.php';
		$host     = APP_ROOT_DIR . DS . 'configs' . DS . $hostName . '.' . strtolower(APP_ENV) . '.php';
		$file 	  = file_exists($host) ? $host : $default;
		
		$writer   = new Core_Base_Config_Writer_Php();
		$writer->write($file, new Zend_Config($config));
	}
	
	/**
	 * Deletes given setting
	 * 
	 * @param string $module
	 * @param string $key
	 * @return bool
	 */
	public static function delete($module, $key)
	{
		if (!$module || !$key) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'	 => 'Config',
						 ))
					     ->setDbConnection($conn)
					     ->delete($module, $key);
		return true;
	}
	
	/**
	 * Gets value of given setting
	 * 
	 * @param string $module Name of module
	 * @param string $key Name of key
	 * @param string $default The default value
	 * @return string
	 */
	public static function get($module, $key, $default = null)
	{
		$conn	   = Core_Services_Db::getConnection();
		$configDao = Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'	 => 'Config',
									  ))
									  ->setDbConnection($conn);
		$value     = $configDao->get($module, $key);
		return ($value == null) ? $default : $value;
	}

	/**
	 * Replaces setting. If the app cannot find the setting, it will create a new one.
	 * In the other case, it will update the existence setting.
	 * 
	 * @param string $module
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public static function set($module, $key, $value)
	{
		if (!$module || !$key) {
			return;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'	 => 'Config',
						 ))
					     ->setDbConnection($conn)
					     ->set($module, $key, $value);
	}
}
