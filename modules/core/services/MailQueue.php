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
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_MailQueue
{
	/**
	 * Finds emails in queue by given collection of conditions
	 * 
	 * @param array $criteria Can consist of the following keys:
	 * - success: 0 or 1
	 * - max_attempts
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'MailQueue',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Increases the number of sending attempts
	 * 
	 * @param Core_Models_MailQueue $mailQueue The mail instance
	 * @return bool
	 */
	public static function increaseAttempts($mailQueue)
	{
		if ($mailQueue == null || !($mailQueue instanceof Core_Models_MailQueue)) {
			throw new Exception('The param is not an instance of Core_Models_MailQueue');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'MailQueue',
						 ))
						 ->setDbConnection($conn)
						 ->increaseAttempts($mailQueue);
		return true;
	}
	
	/**
	 * Adds a mail to queue
	 * 
	 * @param Core_Models_MailQueue $mailQueue The mail instance
	 * @return string Id of newly created queue
	 */
	public static function queue($mailQueue)
	{
		if ($mailQueue == null || !($mailQueue instanceof Core_Models_MailQueue)) {
			throw new Exception('The param is not an instance of Core_Models_MailQueue');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'MailQueue',
								))
								->setDbConnection($conn)
								->queue($mailQueue);
	}
	
	/**
	 * Dequeues mail
	 * 
	 * @param Core_Models_MailQueue $mailQueue The mail instance
	 * @return bool
	 */
	public static function dequeue($mailQueue)
	{
		if ($mailQueue == null || !($mailQueue instanceof Core_Models_MailQueue)) {
			throw new Exception('The param is not an instance of Core_Models_MailQueue');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'MailQueue',
						 ))
						 ->setDbConnection($conn)
						 ->dequeue($mailQueue);
		return true;
	}
}
