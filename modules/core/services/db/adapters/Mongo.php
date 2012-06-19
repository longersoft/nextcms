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

/**
 * The adapter for MongoDB
 */
class Core_Services_Db_Adapters_Mongo extends Core_Services_Db_Abstract
{
	/**
	 * @see Core_Services_Db_Abstract::_connect()
	 */
	protected function _connect($config)
	{
		try {
			$options = array();
			
			// By default, the MongoDB class create a persistent connection.
			// You can set it in the configuration file as follow:
			//		$config['db']['adapter'] = "mongo";
			//		$config['db']['slave'][...slaveServerName...]['persistent'] = "Id1";
			//		$config['db']['master'][...masterServerName...]['persistent'] = "Id2";
			// where Id1, Id2 are IDs of the connections.
			// To disable, set Id value as "false"
			// See http://php.net/manual/en/mongo.construct.php
			if (isset($config['persistent']) && $config['persistent'] != 'false') {
				$options['persist'] = $config['persistent'];
			}
			
			$mongo = new Mongo('mongodb://' . $config['username'] . ':' . $config['password'] . '@' . $config['host'] . ':' . $config['port'] . '/' . $config['dbname'], $options);
			$db    = $mongo->selectDB($config['dbname']);
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
		if (($db = $this->testConnection($config)) === false) {
			return array();	
		}
		$return    = array();
		$databases = $db->listDBs();
		foreach ($databases['databases'] as $index => $array) {
			$return[] = $array['name'];
		}
		return $return;
	}
	
	/**
	 * @see Core_Services_Db_Abstract::getServerVersion()
	 */
	public function getServerVersion()
	{
		// TODO
	}
	
	/**
	 * @see Core_Services_Db_Abstract::testConnection()
	 */
	public function testConnection($config)
	{
		try {
			$connStr = 'mongodb://' . $config['username'] . ':' . $config['password'] . '@' . $config['host'] . ':' . $config['port'];
			if (isset($config['dbname'])) {
				$connStr .= '/' . $config['dbname'];
			}
			$db = new Mongo($connStr);
			return $db;
		} catch (Exception $ex) {
			return false;
		}
	}
}
