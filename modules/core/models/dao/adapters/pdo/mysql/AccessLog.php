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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_AccessLog extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_AccessLog
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_AccessLog($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_AccessLog::add()
	 */
	public function add($accessLog)
	{
		$this->_conn->insert($this->_prefix . 'core_access_log',
							array(
								'user_id'		=> $accessLog->user_id,
								'title'			=> $accessLog->title,
								'url'			=> $accessLog->url,
								'module'		=> $accessLog->module,
								'ip'			=> $accessLog->ip,
								'accessed_date' => $accessLog->accessed_date,
								'params'		=> $accessLog->params,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_access_log');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_AccessLog::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_access_log', array('num_logs' => 'COUNT(*)'));
		if (isset($criteria['module']) && !empty($criteria['module'])) {
			$select->where('module = ?', $criteria['module']);
		}
		if (isset($criteria['from_date']) && !empty($criteria['from_date'])) {
			$select->where('accessed_date >= ?', $criteria['from_date']);
		}
		if (isset($criteria['to_date']) && !empty($criteria['to_date'])) {
			$select->where('accessed_date <= ?', $criteria['to_date']);
		}
		return $select->limit(1)->query()->fetch()->num_logs;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_AccessLog::delete()
	 */
	public function delete($accessLog)
	{
		$this->_conn->delete($this->_prefix . 'core_access_log',
							array(
								'log_id = ?' => $accessLog->log_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_AccessLog::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_access_log');
		if (isset($criteria['module']) && !empty($criteria['module'])) {
			$select->where('module = ?', $criteria['module']);
		}
		if (isset($criteria['ip']) && !empty($criteria['ip'])) {
			$select->where('ip = ?', $criteria['ip']);
		}
		if (isset($criteria['from_date']) && !empty($criteria['from_date'])) {
			$select->where('accessed_date >= ?', $criteria['from_date']);
		}
		if (isset($criteria['to_date']) && !empty($criteria['to_date'])) {
			$select->where('accessed_date <= ?', $criteria['to_date']);
		}
		
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'log_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_AccessLog::getById()
	 */
	public function getById($logId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_access_log')
					->where('log_id = ?', $logId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_AccessLog($row);
	}
}
