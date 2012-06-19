<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Models_Dao_Adapters_Pdo_Mysql_Bookmark extends Core_Base_Models_Dao
	implements File_Models_Dao_Interface_Bookmark
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new File_Models_Bookmark($entity);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Bookmark::add()
	 */
	public function add($bookmark)
	{
		$this->_conn->insert($this->_prefix . 'file_bookmark',
							array(
								'connection_id' => $bookmark->connection_id,
								'name'			=> $bookmark->name,
								'path'			=> $bookmark->path,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'file_bookmark');
	}
	
	/**
	 * @see File_Models_Dao_Interface_Bookmark::delete()
	 */
	public function delete($bookmark)
	{
		$this->_conn->delete($this->_prefix . 'file_bookmark',
							array(
								'connection_id = ?' => $bookmark->connection_id,
								'path = ?'			=> $bookmark->path,
							));
	}
	
	/**
	 * @see File_Models_Dao_Interface_Bookmark::find()
	 */
	public function find($connectionId)
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'file_bookmark')
					   ->where('connection_id = ?', $connectionId)
					   ->order('name ASC')
		 			   ->query()
		 			   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Bookmark::rename()
	 */
	public function rename($bookmark)
	{
		$this->_conn->update($this->_prefix . 'file_bookmark',
							array(
								'name' => $bookmark->name,
							),
							array(
								'bookmark_id = ?' => $bookmark->bookmark_id,
							));
	}
}
