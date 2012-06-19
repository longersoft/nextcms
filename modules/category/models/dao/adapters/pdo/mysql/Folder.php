<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_Models_Dao_Adapters_Pdo_Mysql_Folder extends Core_Base_Models_Dao
	implements Category_Models_Dao_Interface_Folder
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Category_Models_Folder($entity);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Folder::add()
	 */
	public function add($folder)
	{
		$this->_conn->insert($this->_prefix . 'category_folder', 
							array(
								'user_id'	   => $folder->user_id,
								'entity_class' => $folder->entity_class,
								'name'		   => $folder->name,
								'language'	   => $folder->language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'category_folder');
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Folder::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'category_folder', array('num_folders' => 'COUNT(*)'));
		foreach (array('entity_class', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . '= ?', $criteria[$key]);
			}
		}
		return $select->limit(1)->query()->fetch()->num_folders;
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Folder::delete()
	 */
	public function delete($folder)
	{
		$this->_conn->delete($this->_prefix . 'category_folder',
							array(
								'folder_id = ?' => $folder->folder_id,
							));
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Folder::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'category_folder');
		foreach (array('entity_class', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . '= ?', $criteria[$key]);
			}
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'name';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'ASC';
		}
		$select->order($criteria['sort_by'] . ' ' . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Folder::getById()
	 */
	public function getById($folderId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'category_folder')
					->where('folder_id = ?', $folderId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Category_Models_Folder($row);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Folder::rename()
	 */
	public function rename($folder)
	{
		$this->_conn->update($this->_prefix . 'category_folder',
							array(
								'name' => $folder->name,
							), 
							array(
								'folder_id = ?' => $folder->folder_id,
							));
	}
}
