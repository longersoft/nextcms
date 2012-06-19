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
 * @subpackage	services
 * @since		1.0
 * @version		2012-03-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_Services_Folder
{
	/**
	 * Adds new folder
	 * 
	 * @param Category_Models_Folder $folder The folder instance
	 * @return string Id of newly created folder
	 */
	public static function add($folder)
	{
		if (!$folder || !($folder instanceof Category_Models_Folder)) {
			throw new Exception('The input param is not an instance of Category_Models_Folder');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Folder',
								))
								->setDbConnection($conn)
								->add($folder);
	}
	
	/**
	 * Gets the total number of folders by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Folder',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes a given folder
	 * 
	 * @param Category_Models_Folder $folder The folder instance
	 * @return bool
	 */
	public static function delete($folder)
	{
		if (!$folder || !($folder instanceof Category_Models_Folder)) {
			throw new Exception('The input param is not an instance of Category_Models_Folder');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
								'module' => 'category',
								'name'	 => 'Folder',
						  ))
						  ->setDbConnection($conn)
						  ->delete($folder);
		
		// Execute hooks
		Core_Base_Hook_Registry::getInstance()->executeAction('Category_Services_Folder_DeleteFolder', array($folder));			  
		return true;
	}
	
	/**
	 * Searches for folders by given criteria
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Folder',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets folder instance by given Id
	 * 
	 * @param string $folderId The folder's Id
	 * @return Category_Models_Folder
	 */
	public static function getById($folderId)
	{
		if (!$folderId || !is_string($folderId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Folder',
								))
								->setDbConnection($conn)
								->getById($folderId);
	}
	
	/**
	 * Renames a given folder
	 * 
	 * @param Category_Models_Folder $folder The folder instance
	 * @return bool
	 */
	public static function rename($folder)
	{
		if (!$folder || !($folder instanceof Category_Models_Folder)) {
			throw new Exception('The parameter is not an instance of Category_Models_Folder');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
								'module' => 'category',
								'name'	 => 'Folder',
						  ))
						  ->setDbConnection($conn)
						  ->rename($folder);
		return true;
	}	
}
