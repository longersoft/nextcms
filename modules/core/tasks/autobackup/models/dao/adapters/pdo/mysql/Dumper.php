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
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Tasks_Autobackup_Models_Dao_Adapters_Pdo_Mysql_Dumper extends Core_Base_Models_Dao
	implements Core_Tasks_Autobackup_Models_Dao_Interface_Dumper
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Base_Models_Entity($entity);
	}
	
	/**
	 * @see Core_Tasks_Autobackup_Models_Dao_Interface_Dumper::getQueries()
	 */
	public function getQueries()
	{
		$queries = array();
		
		// Get the list of tables
		$tables = $this->_getTables();
		
		foreach ($tables as $table) {
			$queries = array_merge($queries, $this->_getCreatingTableQueries($table));
			$queries = array_merge($queries, $this->_getInsertingDataQueries($table));
		}
		
		return $queries;
	}
	
	private function _getTables()
	{
		$query  = (strlen($this->_prefix) == 0) ? '' : " LIKE '" . $this->_prefix . "%'";
		$query  = 'SHOW TABLES' . $query;
		$rows   = array();
		$result = $this->_conn
					   ->query($query)
					   ->fetchAll();
		foreach ($result as $item) {
			$item	= (array) $item;
			$item	= array_values($item);
			$rows[] = $item[0];
		}
		return $rows;
	}
	
	private function _getCreatingTableQueries($table)
	{
		$query  = 'SHOW CREATE TABLE ' . $table;
		$result = $this->_conn
					   ->query($query)
					   ->fetch();
		$item	= (array) $result;
		$item   = $item['Create Table'] . ';';
		return array($item);
	}
	
	private function _getInsertingDataQueries($table)
	{
		$result = $this->_conn
					   ->select()
					   ->from($table)
					   ->query()
					   ->fetchAll();
		$queries = array();
		foreach ($result as $item) {
			$item = (array) $item;
			$data = '';
			foreach ($item as $key => $value) {
				$data .= is_null($value) ? 'NULL, ' : '\'' . addslashes($value) . '\', ';
			}
			$data = substr($data, 0, -2);
			
			$queries[] = 'INSERT INTO `'. $table . '` VALUES (' . $data . ');';
		}
		
		return $queries;
	}
}
