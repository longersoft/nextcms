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
 * @subpackage	views
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_View_Helper_Message extends Zend_View_Helper_Abstract
{
	/**
	 * Gets the view helper instance
	 * 
	 * @return Message_View_Helper_Message
	 */
	public function message()
	{
		return $this;
	}
	
	/**
	 * Counts the number of messages by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function countThreads($criteria)
	{
		Core_Services_Db::connect('slave');
		return Message_Services_Message::countThreads($criteria);
	}
	
	/**
	 * Counts the deleted messages of current user
	 * 
	 * @return int
	 */
	public function countDeletedMessages()
	{
		return $this->countThreads(array(
			'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
			'deleted' => '1',
		));
	}
	
	/**
	 * Counts the unread messages of current user
	 * 
	 * @param string $folderId Id of message folder
	 * @return int
	 */
	public function countUnreadMessages($folderId = Message_Models_Folder::FOLDER_INBOX)
	{
		return $this->countThreads(array(
			'folder_id' => $folderId,
			'user_id'	=> Zend_Auth::getInstance()->getIdentity()->user_id,
			'unread'	=> '1',
			'deleted'	=> '0',		// Don't count the unread messages in trash
		));
	}
	
	/**
	 * Gets the message folder by given folder's Id
	 * 
	 * @param string $folderId The Id of folder
	 * @return Message_Models_Folder
	 */
	public function getFolder($folderId)
	{
		Core_Services_Db::connect('slave');
		return Message_Services_Folder::getById($folderId);
	}
	
	/**
	 * Gets the list of recipients
	 * 
	 * @param array $userIds Array of recipient'd Ids
	 * @return array of recipients which each item is an instance of Core_Models_User
	 */
	public function getRecipients($userIds)
	{
		Core_Services_Db::connect('slave');
		return Message_Services_Message::getRecipients($userIds);
	}
	
	/**
	 * Shows the number of unread messages in the back-end menu
	 *  
	 * @return void
	 */
	public function showUnreadMessages()
	{
		echo $this->view->render('_partial/_unreadMessagesMenu.phtml');
	}
}
