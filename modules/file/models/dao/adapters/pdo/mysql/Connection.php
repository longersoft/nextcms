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

class File_Models_Dao_Adapters_Pdo_Mysql_Connection extends Core_Base_Models_Dao
	implements File_Models_Dao_Interface_Connection
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new File_Models_Connection($entity);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Connection::add()
	 */
	public function add($connection)
	{
		$this->_conn->insert($this->_prefix . 'file_connection',
							array(
								'type'		=> $connection->type,
								'name'		=> $connection->name,
								'server'	=> $connection->server,
								'port'		=> $connection->port,
								'user_name' => $connection->user_name,
								'password'	=> $connection->password,
								'init_path' => $connection->init_path,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'file_connection');
	}
	
	/**
	 * @see File_Models_Dao_Interface_Connection::delete()
	 */
	public function delete($connection)
	{
		$this->_conn->delete($this->_prefix . 'file_bookmark',
							array(
								'connection_id = ?' => $connection->connection_id,
							));
		$this->_conn->delete($this->_prefix . 'file_connection',
							array(
								'connection_id = ?' => $connection->connection_id,
							));
	}
	
	/**
	 * @see File_Models_Dao_Interface_Connection::find()
	 */
	public function find()
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'file_connection')
					   ->order('connection_id DESC')
		 			   ->query()
		 			   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Connection::getById()
	 */
	public function getById($connectionId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'file_connection')
					->where('connection_id = ?', $connectionId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new File_Models_Connection($row);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Connection::rename()
	 */
	public function rename($connection)
	{
		$this->_conn->update($this->_prefix . 'file_connection',
							array(
								'name' => $connection->name,
							),
							array(
								'connection_id = ?' => $connection->connection_id,
							));
	}
	
	/**
	 * @see File_Models_Dao_Interface_Connection::update()
	 */
	public function update($connection)
	{
		$data = array(
			'type'		=> $connection->type,
			'name'		=> $connection->name,
			'server'	=> $connection->server,
			'port'		=> $connection->port,
			'user_name' => $connection->user_name,
			'init_path' => $connection->init_path,
		);
		switch (true) {
			case (is_null($connection->password)):
				break;
			case ($connection->password == ''):
				$data['password'] = null;
				break;
			case ($connection->password != null):
				$data['password'] = $connection->password;
				break;
		}
		
		$this->_conn->update($this->_prefix . 'file_connection',
							$data,
							array(
								'connection_id = ?' => $connection->connection_id,
							));
	}
}
