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

interface Message_Models_Dao_Interface_Folder
{
	/**
	 * Adds new folder
	 * 
	 * @param Message_Models_Folder $folder The folder instance
	 * @return string Id of newly created folder
	 */
	public function add($folder);
	
	/**
	 * Deletes given message folder
	 * 
	 * @param Message_Models_Folder $folder The folder instance
	 * @return void
	 */
	public function delete($folder);	
	
	/**
	 * Finds the private message folders of given user
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array());
	
	/**
	 * Gets message folder instance by given Id
	 * 
	 * @param string $folderId Id of folder
	 * @return Message_Models_Folder|null
	 */
	public function getById($folderId);
	
	/**
	 * Renames given message folder
	 * 
	 * @param Message_Models_Folder $folder The folder instance
	 * @return void
	 */
	public function rename($folder);
}
