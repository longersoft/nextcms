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

class Core_Services_Db
{
	/**
	 * The default database prefix
	 * 
	 * @var string
	 */
	const DEFAULT_PREFIX = '';
	
	/**
	 * @var string
	 */
	const KEY_PREFIX 	 = 'Core_Services_Db_DbPrefix';
	
	/**
	 * Gets database prefix
	 * 
	 * @return string
	 */
	public static function getDbPrefix()
	{
		if (!Zend_Registry::isRegistered(self::KEY_PREFIX)) {
			$config = Core_Services_Config::getAppConfigs();
			
			// Note that I use === operator that allows user to use empty prefix
			$prefix = (null === $config['db']['prefix']) ? self::DEFAULT_PREFIX : $config['db']['prefix'];
			Zend_Registry::set(self::KEY_PREFIX, $prefix);
		}
		return Zend_Registry::get(self::KEY_PREFIX);
	}

	/**
	 * Gets instance of database adapter
	 * 
	 * @param string $adapter
	 * @return Core_Services_Db_Abstract
	 */
	public static function factory($adapter = null)
	{
		if ($adapter == null) {
			$config  = Core_Services_Config::getAppConfigs();
			$adapter = $config['db']['adapter'];
		}
		$adapter = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));
		$class 	 = 'Core_Services_Db_Adapters_' . $adapter;
		if (!class_exists($class)) {
			throw new Exception('Does not support ' . $adapter . ' connection');
		}
		return new $class($adapter);
	}
	
	/**
	 * Connects to master or slave server
	 * 
	 * @param string $type Can be "master" or "slave"
	 * @return mixed
	 */
	public static function connect($type)
	{
		$db   = self::factory();
		$conn = $db->getConnection($type);
		
		Zend_Registry::set('db', $conn);
		return $conn;
	}
	
	/**
	 * Gets the current connection which is registered by Zend_Registry.
	 * Uses the connect() and getConnection() methods, I can set the connection in the controller action:
	 * 
	 * // Controller action
	 * Core_Services_Db::connect('slave');
	 * 
	 * // and get the connection in the services layer:
	 * $conn = Core_Services_Db::getConnection();
	 * 
	 * @return mixed
	 */
	public static function getConnection()
	{
		if (!Zend_Registry::isRegistered('db')) {
			throw new Exception('The connection has not been registered with Zend_Registry');
		}
		return Zend_Registry::get('db');
	}
}
