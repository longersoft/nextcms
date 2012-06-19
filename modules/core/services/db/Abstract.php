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

abstract class Core_Services_Db_Abstract
{
	/**
	 * @var string
	 */
	const KEY_CONNECTION_TYPE = 'Core_Services_Db_Abstract_ConnType';
	
	/**
	 * Gets database connection
	 * 
	 * @param string $type Type of connection. Must be slave or master
	 * @return mixed
	 */
	public function getConnection($type)
	{
		$key = self::KEY_CONNECTION_TYPE . '_' . $type;
		if (!Zend_Registry::isRegistered($key)) {
			$config  = Core_Services_Config::getAppConfigs();
			$servers = $config['db'][$type];
			
			// Connect to random server
			$random  = array_rand($servers);
			
			// Get database prefix
			$prefix  = (null == $config['db']['prefix']) ? Core_Services_Db::DEFAULT_PREFIX : $config['db']['prefix'];
			
			$servers[$random]['prefix'] = $prefix;
			
			// Set the charset if it is not set
			if (!isset($servers[$random]['charset'])) {
				$servers[$random]['charset'] = 'utf8';
			}
			
			$db = $this->_connect($servers[$random]);
			if ($db === false) {
				return null;
			}
			
			Zend_Registry::set($key, $db);
		}
		return Zend_Registry::get($key);
	}
	
	/**
	 * Gets master connection
	 * 
	 * @return mixed
	 */
	public function getMasterConnection()
	{
		return $this->getConnection('master');
	}
	
	/**
	 * Gets slave connection
	 * 
	 * @return mixed
	 */
	public function getSlaveConnection()
	{
		return $this->getConnection('slave');
	}
	
	////////// ABSTRACT METHODS //////////
	
	/**
	 * Connects to the database server
	 * 
	 * @param array $config Database connection settings, includes parameters:
	 * - host
	 * - port
	 * - dbname
	 * - username
	 * - password
	 * - charset
	 * @return mixed Database connection
	 */
	protected abstract function _connect($config);
	
	/**
	 * Gets available databases
	 * 
	 * @param array $config Database connection settings, includes parameters:
	 * - host
	 * - port
	 * - username
	 * - password
	 * @return array
	 */
	public abstract function getDatabases($config);
	
	/**
	 * Gets database server version
	 * 
	 * @return string
	 */
	public abstract function getServerVersion();

	/**
	 * Tests the connection
	 * 
	 * @param array $config Database connection settings, includes parameters:
	 * - host
	 * - port
	 * - username
	 * - password
	 * @return bool
	 */
	public abstract function testConnection($config);
}
