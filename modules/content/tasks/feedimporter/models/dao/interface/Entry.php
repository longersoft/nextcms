<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Content_Tasks_Feedimporter_Models_Dao_Interface_Entry
{
	/**
	 * Checks if an entry with given link is already imported or not
	 * 
	 * @param string $link
	 * @return bool
	 */
	public function exist($link);
	
	/**
	 * Add new entry
	 * 
	 * @param Content_Tasks_Feedimporter_Models_Entry $entry
	 * @return string Id of newly added entry
	 */
	public function add($entry);
}
