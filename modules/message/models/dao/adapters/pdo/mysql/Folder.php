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

class Message_Models_Dao_Adapters_Pdo_Mysql_Folder extends Core_Base_Models_Dao
	implements Message_Models_Dao_Interface_Folder
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Message_Models_Folder($entity);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Folder::add()
	 */
	public function add($folder)
	{
		$this->_conn->insert($this->_prefix . 'message_folder',
							array(
								'user_id' => $folder->user_id,
								'name'    => $folder->name,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'message_folder');
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Folder::delete()
	 */
	public function delete($folder)
	{
		$this->_conn->delete($this->_prefix . 'message_folder',
							array(
								'folder_id = ?' => $folder->folder_id,
								// To prevent user from deleting folder of other users
								'user_id = ?'	=> $folder->user_id,
							));
		$this->_conn->update($this->_prefix . 'message_recipient',
							array(
								'folder_id' => Message_Models_Folder::FOLDER_INBOX,
							),
							array(
								'folder_id = ?' => $folder->folder_id,
							));
		$this->_conn->delete($this->_prefix . 'message_filter',
							array(
								'folder_id = ?' => $folder->folder_id,
								'user_id = ?'	=> $folder->user_id,
							));
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Folder::find()
	 */
	public function find($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'message_folder')
					   ->where('user_id = ?', $criteria['user_id']);
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'name';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'ASC';
		}
		$select->order($criteria['sort_by'] . ' ' . $criteria['sort_dir']);
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Folder::getById()
	 */
	public function getById($folderId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'message_folder')
					->where('folder_id = ?', $folderId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Message_Models_Folder($row);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Folder::rename()
	 */
	public function rename($folder)
	{
		$this->_conn->update($this->_prefix . 'message_folder',
							array(
								'name' => $folder->name,
							),
							array(
								'folder_id = ?' => $folder->folder_id,
							));
	}
}
