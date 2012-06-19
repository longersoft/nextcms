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

interface File_Models_Dao_Interface_Bookmark
{
	/**
	 * Adds new bookmark
	 * 
	 * @param File_Models_Bookmark $bookmark
	 * @return string Id of newly added bookmark
	 */
	public function add($bookmark);
	
	/**
	 * Deletes bookmark that is defined by the connection and path
	 * 
	 * @param File_Models_Bookmark $bookmark
	 * @return void
	 */
	public function delete($bookmark);
	
	/**
	 * Gets all bookmarks by given connection
	 * 
	 * @param string $connectionId
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($connectionId);
	
	/**
	 * Renames given bookmark
	 * 
	 * @param File_Models_Bookmark $bookmark
	 * @return void
	 */
	public function rename($bookmark);
}
