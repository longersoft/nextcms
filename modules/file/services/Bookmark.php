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

class File_Services_Bookmark
{
	/**
	 * Adds new bookmark
	 * 
	 * @param File_Models_Bookmark $bookmark
	 * @return string Id of newly added bookmark
	 */
	public static function add($bookmark)
	{
		if (!$bookmark || !($bookmark instanceof File_Models_Bookmark)) {
			throw new Exception('The input param is not an instance of File_Models_Bookmark');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Bookmark',
								))
								->setDbConnection($conn)
								->add($bookmark);
	}
	
	/**
	 * Deletes bookmark that is defined by the connection and path
	 * 
	 * @param File_Models_Bookmark $bookmark
	 * @return bool
	 */
	public static function delete($bookmark)
	{
		if (!$bookmark || !($bookmark instanceof File_Models_Bookmark)) {
			throw new Exception('The input param is not an instance of File_Models_Bookmark');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Bookmark',
								))
								->setDbConnection($conn)
								->delete($bookmark);
		return true;
	}
	
	/**
	 * Gets all bookmarks by given connection
	 * 
	 * @param string $connectionId
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($connectionId)
	{
		if (!$connectionId || !is_string($connectionId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Bookmark',
								))
								->setDbConnection($conn)
								->find($connectionId);
	}
	
	/**
	 * Renames given bookmark
	 * 
	 * @param File_Models_Bookmark $bookmark
	 * @return bool
	 */
	public static function rename($bookmark)
	{
		if (!$bookmark || !($bookmark instanceof File_Models_Bookmark)) {
			throw new Exception('The input param is not an instance of File_Models_Bookmark');
		} 
		if ($bookmark->isNullOrEmpty($bookmark->bookmark_id) || $bookmark->isNullOrEmpty($bookmark->name)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Bookmark',
								))
								->setDbConnection($conn)
								->rename($bookmark);
		return true;
	}
}
