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

interface Content_Tasks_Publisher_Models_Dao_Interface_Article
{
	/**
	 * Activates all articles which have the publishing date earlier than the given date
	 * 
	 * @param string $date The date to compare
	 * @return void
	 */
	public function activate($date);	
}
