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
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_MailQueue
{
	/**
	 * Finds emails in queue by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Increases the number of sending attempts
	 * 
	 * @param Core_Models_MailQueue $mailQueue The mail instance
	 * @return void
	 */
	public function increaseAttempts($mailQueue);
	
	/**
	 * Adds a mail to queue
	 * 
	 * @param Core_Models_MailQueue $mailQueue The mail instance
	 * @return string Id of newly created queue
	 */
	public function queue($mailQueue);
	
	/**
	 * Dequeues mail
	 * 
	 * @param Core_Models_MailQueue $mailQueue The mail instance
	 * @return void
	 */
	public function dequeue($mailQueue);
}
