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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Services_Message
{
	/**
	 * Adds new private message
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @return string Id of newly created message
	 */
	public static function add($message)
	{
		if ($message == null || !($message instanceof Message_Models_Message)) {
			throw new Exception('The param is not an instance of Message_Models_Message');
		}
		$message->sanitize();
		$conn	   = Core_Services_Db::getConnection();
		$messageId = Core_Services_Dao::factory(array(
										'module' => 'message',
										'name'   => 'Message',
									  ))
									  ->setDbConnection($conn)
									  ->add($message);
		// Inform recipients of incoming messages
		if (Core_Services_Config::get('message', 'email_enabled', 'false') == 'true') {
			$recipients = self::getRecipients($message->to_address);
			$sender		= Core_Services_User::getById($message->sent_user);
			if ($sender && $recipients) {
				$view	  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
				
				// Get the mail template
				$template = Core_Services_Mail::getBuiltinTemplate('message', 'message_sent_template');
				
				// Replace the macros
				$search   = array('###username###', '###sender###', '###subject###', '###content###', '###link###');
				
				foreach ($recipients as $recipient) {
					$replace = array($recipient->user_name, $sender->user_name, $message->subject, $message->content, $view->serverUrl() . $view->url(array(), 'core_auth_login'));
					$subject = str_replace($search, $replace, $template['subject']);
					$content = str_replace($search, $replace, $template['content']);
					
					Core_Services_MailQueue::queue(new Core_Models_MailQueue(array(
						'from_name'	  => $template['from_name'],
						'from_email'  => $template['from_email'],
						'to_name'	  => $recipient->user_name,
						'to_email'	  => $recipient->email,
						'subject'	  => $subject,
						'content'	  => $content,
						'queued_date' => date('Y-m-d H:i:s'),
					)));
				}
			}
		}
		
		return $messageId;
	}
	
	/**
	 * Counts the number of private messages by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (!isset($criteria['user_id'])) {
			throw new Exception("The criteria has to define the user's Id");
		}
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Message',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Gets the number of threads
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function countThreads($criteria = array())
	{
		if (!isset($criteria['user_id'])) {
			throw new Exception("The criteria has to define the user's Id");
		}
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}	
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Message',
								))
								->setDbConnection($conn)
								->countThreads($criteria);
	}
	
	/**
	 * Deletes given message of user
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function delete($message, $user)
	{
		if (!$message || !($message instanceof Message_Models_Message)) {
			throw new Exception('The first param is not an instance of Message_Models_Message');
		}
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Message',
						 ))
						 ->setDbConnection($conn)
						 ->delete($message, $user);
		return true;
	}
	
	/**
	 * Empties the trash of given user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function emptyTrash($user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Message',
						 ))
						 ->setDbConnection($conn)
						 ->emptyTrash($user);
		return true;
	}
	
	/**
	 * Finds private messages by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		if (!isset($criteria['user_id'])) {
			throw new Exception("The criteria has to define the user's Id");
		}
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Message',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Finds the latest private messages which belong to separate threads
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function findThreads($criteria = array(), $offset = null, $count = null)
	{
		if (!isset($criteria['user_id'])) {
			throw new Exception("The criteria has to define the user's Id");
		}
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Message',
								))
								->setDbConnection($conn)
								->findThreads($criteria, $offset, $count);
	}
	
	/**
	 * Gets private message instance by given Id
	 * 
	 * @param string $messageId Message's Id
	 * @return Message_Models_Message|null
	 */
	public static function getById($messageId)
	{
		if ($messageId == null || empty($messageId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Message',
								))
								->setDbConnection($conn)
								->getById($messageId);
	}
	
	/**
	 * Gets the list of recipients
	 * 
	 * @param array $userIds Array of recipient'd Ids
	 * @return array of recipients which each item is an instance of Core_Models_User
	 */
	public static function getRecipients($userIds)
	{
		if (is_string($userIds)) {
			$userIds = explode(',', $userIds);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Message',
								))
								->setDbConnection($conn)
								->getRecipients($userIds);
	}
	
	/**
	 * Moves message to other folder
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @param Message_Models_Folder $folder The folder instance
	 * @throws Exception
	 * @return bool
	 */
	public static function move($message, $user, $folder)
	{
		$toAddress = explode(',', $message->to_address);
		if (!in_array($user->user_id, $toAddress)) {
			throw new Exception('It is not possible to move message of other users');
		}
		if ($folder == null || $folder->user_id != $user->user_id) {
			throw new Exception('It is not possible to move message to folder created by other users');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Message',
						 ))
						 ->setDbConnection($conn)
						 ->move($message, $user, $folder);
		return true;
	}
	
	/**
	 * Marks message as read/unread
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function toggleRead($message, $user)
	{
		if (!$message || !($message instanceof Message_Models_Message)) {
			throw new Exception('The first param is not an instance of Message_Models_Message');
		}
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Message',
						 ))
						 ->setDbConnection($conn)
						 ->toggleRead($message, $user);
		return true;
	}
	
	/**
	 * Adds star or removes star
	 * 
	 * @param Message_Models_Message $message The message instance
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function toggleStar($message, $user)
	{
		if (!$message || !($message instanceof Message_Models_Message)) {
			throw new Exception('The first param is not an instance of Message_Models_Message');
		}
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Message',
						 ))
						 ->setDbConnection($conn)
						 ->toggleStar($message, $user);
		return true;
	}
}
