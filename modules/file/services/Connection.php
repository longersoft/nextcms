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
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Services_Connection
{
	/**
	 * Adds new connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return string Id of newly added connection
	 */
	public static function add($connection)
	{
		if (!$connection || !($connection instanceof File_Models_Connection) || $connection->isNullOrEmpty($connection->name)) {
			throw new Exception('The input param is not an instance of File_Models_Connection');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'	 => 'Connection',
								))
								->setDbConnection($conn)
								->add($connection);
	}
	
	/**
	 * Deletes a connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return bool
	 */
	public static function delete($connection)
	{
		if (!$connection || !($connection instanceof File_Models_Connection)) {
			throw new Exception('The input param is not an instance of File_Models_Connection');
		}
		if ($connection->isNullOrEmpty($connection->connection_id)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'	 => 'Connection',
						 ))
						 ->setDbConnection($conn)
						 ->delete($connection);
		return true;
	}
	
	/**
	 * Gets the list of connections
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find()
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'	 => 'Connection',
								))
								->setDbConnection($conn)
								->find();
	}
	
	/**
	 * Gets the connection instance by given id
	 * 
	 * @param string $connectionId
	 * @return File_Models_Connection|null
	 */
	public static function getById($connectionId)
	{
		if (!$connectionId || !is_string($connectionId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'	 => 'Connection',
								))
								->setDbConnection($conn)
								->getById($connectionId);
	}

	/**
	 * Renames given connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return bool
	 */
	public static function rename($connection)
	{
		if (!$connection || !($connection instanceof File_Models_Connection)) {
			throw new Exception('The input param is not an instance of File_Models_Connection');
		}
		if ($connection->isNullOrEmpty($connection->connection_id) || $connection->isNullOrEmpty($connection->name)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'	 => 'Connection',
								))
								->setDbConnection($conn)
								->rename($connection);
		return true;
	}
	
	/**
	 * Updates given connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return bool
	 */
	public static function update($connection)
	{
		if (!$connection || !($connection instanceof File_Models_Connection)) {
			throw new Exception('The input param is not an instance of File_Models_Connection');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'	 => 'Connection',
								))
								->setDbConnection($conn)
								->update($connection);
		return true;
	}
}
