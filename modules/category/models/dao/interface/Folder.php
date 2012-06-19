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

interface Category_Models_Dao_Interface_Folder
{
	/**
	 * Adds new folder
	 * 
	 * @param Category_Models_Folder $folder The folder instance
	 * @return string Id of newly created folder
	 */
	public function add($folder);
	
	/**
	 * Gets the total number of folders by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes a given folder
	 * 
	 * @param Category_Models_Folder $folder The folder instance
	 * @return void
	 */
	public function delete($folder);
	
	/**
	 * Searches for folders by given criteria
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets folder instance by given Id
	 * 
	 * @param string $folderId The folder's Id
	 * @return Category_Models_Folder
	 */
	public function getById($folderId);
	
	/**
	 * Renames a given folder
	 * 
	 * @param Category_Models_Folder $folder The folder instance
	 * @return void
	 */
	public function rename($folder);
}
