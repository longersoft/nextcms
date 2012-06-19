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

class Core_Models_Dao_Adapters_Pdo_Mysql_Error extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Error
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Error($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Error::add()
	 */
	public function add($error)
	{
		$this->_conn->insert($this->_prefix . 'core_error',
							array(
								'created_user' => $error->created_user,
								'created_date' => $error->created_date,
								'uri'		   => $error->uri,
								'module'	   => $error->module,
								'controller'   => $error->controller,
								'action'	   => $error->action,
								'class'		   => $error->class,
								'file'		   => $error->file,
								'line'		   => $error->line,
								'message'	   => $error->message,
								'trace'		   => $error->trace,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_error');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Error::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_error', array('num_errors' => 'COUNT(*)'));
		if (isset($criteria['module']) && !empty($criteria['module'])) {
			$select->where('module = ?', $criteria['module']);
		}
		if (isset($criteria['from_date']) && !empty($criteria['from_date'])) {
			$select->where('created_date >= ?', $criteria['from_date']);
		}
		if (isset($criteria['to_date']) && !empty($criteria['to_date'])) {
			$select->where('created_date <= ?', $criteria['to_date']);
		}
		return $select->limit(1)->query()->fetch()->num_errors;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Error::delete()
	 */
	public function delete($error)
	{
		$this->_conn->delete($this->_prefix . 'core_error',
							array(
								'error_id = ?' => $error->error_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Error::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_error');
		if (isset($criteria['module']) && !empty($criteria['module'])) {
			$select->where('module = ?', $criteria['module']);
		}
		if (isset($criteria['from_date']) && !empty($criteria['from_date'])) {
			$select->where('created_date >= ?', $criteria['from_date']);
		}
		if (isset($criteria['to_date']) && !empty($criteria['to_date'])) {
			$select->where('created_date <= ?', $criteria['to_date']);
		}
		
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'error_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'ASC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Error::getById()
	 */
	public function getById($errorId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_error')
					->where('error_id = ?', $errorId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Error($row);
	}
}
