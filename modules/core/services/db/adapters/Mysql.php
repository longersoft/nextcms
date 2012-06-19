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

class Core_Services_Db_Adapters_Mysql extends Core_Services_Db_Abstract
{
	/**
	 * @see Core_Services_Db_Abstract::_connect()
	 */
	protected function _connect($config)
	{
		// Support persistent connection
		// If you want to use persistent connection, set the persistent options
		// to true in the application configuration as follow:
		// 		$config['db']['adapter'] = "mysql";
		//		$config['db']['slave'][...slaveServerName...]['persistent'] = "true";
		//		$config['db']['master'][...masterServerName...]['persistent'] = "true";
		// The default value is false.
		
		// Set the adapter namespace, so Zend_Db can find the full class of adapter as Core_Base_Db_Adapters_Mysql
		$config['adapterNamespace'] = 'Core_Base_Db_Adapters';
		
		try {
			$db = Zend_Db::factory('Mysql', $config);
			$db->setFetchMode(Zend_Db::FETCH_OBJ);
			return $db;
		} catch (Exception $ex) {
			return false;
		}
	}
	
	/**
	 * @see Core_Services_Db_Abstract::getDatabases()
	 */
	public function getDatabases($config)
	{
		if (!$this->testConnection($config)) {
			return array();
		}
		$rows 	   = mysql_list_dbs();
		$databases = array();
		while ($row = mysql_fetch_object($rows)) {
			$databases[] = $row->Database;
		}
		return $databases;
	}
	
	/**
	 * @see Core_Services_Db_Abstract::getServerVersion()
	 */
	public function getServerVersion()
	{
		$conn	 = $this->getSlaveConnection();
		$version = $conn->getServerVersion();
		return 'MySQL v' . $version;
	}
	
	/**
	 * @see Core_Services_Db_Abstract::testConnection()
	 */
	public function testConnection($config)
	{
		$link = @mysql_connect($config['host'] . ':' . $config['port'], $config['username'], $config['password']);
		if (!$link) {
			return false;
		}
		return true;
	}
}
