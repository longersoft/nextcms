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
 * @subpackage	services
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Services_Folder
{
	/**
	 * Adds new folder
	 * 
	 * @param Message_Models_Folder $folder The folder instance
	 * @return string Id of newly created folder
	 */
	public static function add($folder)
	{
		if ($folder == null || !($folder instanceof Message_Models_Folder)) {
			throw new Exception('The param is not an instance of Message_Models_Folder');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Folder',
								))
								->setDbConnection($conn)
								->add($folder);
	}
	
	/**
	 * Deletes given message folder
	 * 
	 * @param Message_Models_Folder $folder The folder instance
	 * @return bool
	 */
	public static function delete($folder)
	{
		if (!$folder || !($folder instanceof Message_Models_Folder)) {
			throw new Exception('The param is not an instance of Message_Models_Folder');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Folder',
						 ))
						 ->setDbConnection($conn)
						 ->delete($folder);
		return true;
	}
	
	/**
	 * Finds the private message folders
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Folder',
								))
								->setDbConnection($conn)
								->find($criteria);
	}
	
	/**
	 * Gets message folder instance by given Id
	 * 
	 * @param string $folderId Id of folder
	 * @return Message_Models_Folder|null
	 */
	public static function getById($folderId)
	{
		if ($folderId == null || empty($folderId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Folder',
								))
								->setDbConnection($conn)
								->getById($folderId);
	}
	
	/**
	 * Renames given message folder
	 * 
	 * @param Message_Models_Folder $folder The folder instance
	 * @return bool
	 */
	public static function rename($folder)
	{
		if (!$folder || !($folder instanceof Message_Models_Folder)) {
			throw new Exception('The param is not an instance of Message_Models_Folder');
		}
		if ($folder->name == null || empty($folder->name)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Folder',
						 ))
						 ->setDbConnection($conn)
						 ->rename($folder);
		return true;
	}
}
