<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Tasks_Cleaner_Models_Dao_Interface_AccessLog
{
	/**
	 * Deletes all the access logs created on day which are earlier than current day 
	 * given number of days
	 * 
	 * @param int $days The number of days
	 * @return void
	 */
	public function deleteAccessLogs($days);
}
