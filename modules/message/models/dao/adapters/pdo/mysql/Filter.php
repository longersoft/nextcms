<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	models
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Models_Dao_Adapters_Pdo_Mysql_Filter extends Core_Base_Models_Dao
	implements Message_Models_Dao_Interface_Filter
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Message_Models_Filter($entity);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Filter::add()
	 */
	public function add($filter)
	{
		$this->_conn->insert($this->_prefix . 'message_filter',
							array(
								'user_id'	    => $filter->user_id,
								'object'	    => $filter->object,
								'condition'	    => $filter->condition,
								'comparison_to' => $filter->comparison_to,
								'actions'	    => $filter->actions,
								'folder_id'	    => $filter->folder_id,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'message_filter');
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Filter::delete()
	 */
	public function delete($filter)
	{
		$this->_conn->delete($this->_prefix . 'message_filter',
							array(
								'filter_id = ?' => $filter->filter_id,
								'user_id = ?'	=> $filter->user_id,
							));
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Filter::find()
	 */
	public function find($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'message_filter')
					   ->where('user_id = ?', $criteria['user_id']);
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'filter_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		$select->order($criteria['sort_by'] . ' ' . $criteria['sort_dir']);
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Filter::getById()
	 */
	public function getById($filterId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'message_filter')
					->where('filter_id = ?', $filterId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Message_Models_Filter($row);
	}
}
