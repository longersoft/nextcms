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

interface Message_Models_Dao_Interface_Message
{
	/**
	 * Adds new private message
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @return string Id of newly created message
	 */
	public function add($message);
	
	/**
	 * 
	 * Check if given user can download the attachment or not.
	 * Only allow users who receive the private message to download the attachment.
	 * 
	 * @param string $path The path of attachment
	 * @param Core_Models_User $user The user instance
	 * @return bool Returns TRUE or FALSE. If TRUE, updates the last download date, 
	 * last download user and increase the number of downloads
	 */
	public function canDownloadAttachment($path, $user);
	
	/**
	 * Counts the number of private messages by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Gets the number of threads
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function countThreads($criteria = array());
	
	/**
	 * Deletes given message of user
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function delete($message, $user);

	/**
	 * Empties the trash of given user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function emptyTrash($user);
	
	/**
	 * Finds private messages by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Finds the latest private messages which belong to separate threads
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function findThreads($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets private message instance by given Id
	 * 
	 * @param string $messageId Message's Id
	 * @return Message_Models_Message|null
	 */
	public function getById($messageId);
	
	/**
	 * Gets the list of recipients
	 * 
	 * @param array $userIds Array of recipient'd Ids
	 * @return array of recipients which each item is an instance of Core_Models_User
	 */
	public function getRecipients($userIds);
	
	/**
	 * Moves message to other folder
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @param Message_Models_Folder $folder The folder instance
	 * @return void
	 */
	public function move($message, $user, $folder);
	
	/**
	 * Marks message as read/unread
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function toggleRead($message, $user);
	
	/**
	 * Adds star or removes star
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function toggleStar($message, $user);
}
