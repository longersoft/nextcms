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

class Core_Services_Db_Adapters_Pdo_Mysql extends Core_Services_Db_Abstract
{
	/**
	 * @see Core_Services_Db_Abstract::_connect()
	 */
	protected function _connect($config)
	{
		try {
			$db = Zend_Db::factory('Pdo_Mysql', $config);
			$db->setFetchMode(Zend_Db::FETCH_OBJ);
			$db->query("SET CHARACTER SET ?", array($config['charset']));
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
		// FIXME: Use PDO adapter
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
		$conn = $this->getSlaveConnection();
		$row  = $conn->query('SELECT VERSION() AS ver')->fetch();
		return 'MySQL v' . $row->ver;
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
