<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_Services_Comment
{
	/**
	 * Adds new comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return string Id of newly created comment
	 */
	public static function add($comment)
	{
		if (!$comment || !($comment instanceof Comment_Models_Comment)) {
			throw new Exception('The param is not an instance of Comment_Models_Comment');
		}
		$comment->sanitize();
		
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'comment',
									'name'   => 'Comment',
								))
								->setDbConnection($conn)
								->add($comment);
	}
	
	/**
	 * Gets the number of comments
	 * 
	 * @param array $criteria Contains the following memebers:
	 * - entity_id, entity_class, entity_module: These members are required to define the thread
	 * if you want to count the number of comments in the same thread
	 * - keyword
	 * - status
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'comment',
									'name'   => 'Comment',
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
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'comment',
									'name'   => 'Comment',
								))
								->setDbConnection($conn)
								->countThreads($criteria);
	}
	
	/**
	 * Deletes given comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return bool
	 */
	public static function delete($comment)
	{
		if (!$comment || !($comment instanceof Comment_Models_Comment)) {
			throw new Exception('The param is not an instance of Comment_Models_Comment');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'comment',
							'name'   => 'Comment',
						 ))
						 ->setDbConnection($conn)
						 ->delete($comment);
		return true;
	}
	
	/**
	 * Finds the comments which belong to same thread
	 * 
	 * @param array $criteria Contains the following memebers:
	 * - entity_id, entity_class, entity_module: These members are required to define the thread
	 * if you want to count the number of comments in the same thread
	 * - keyword
	 * - is_active
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'comment',
									'name'   => 'Comment',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Finds the latest comments which belong to separate threads
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function findThreads($criteria = array(), $offset = null, $count = null)
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'comment',
									'name'   => 'Comment',
								))
								->setDbConnection($conn)
								->findThreads($criteria, $offset, $count);
	}
	
	/**
	 * Gets comment by given Id
	 * 
	 * @param string $commentId
	 * @return Comment_Models_Comment|null
	 */
	public static function getById($commentId)
	{
		if (!$commentId) {
			throw new Exception("The comment's Id is required");
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'comment',
									'name'   => 'Comment',
								))
								->setDbConnection($conn)
								->getById($commentId);
	}
	
	/**
	 * Reports given comment as spam
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return bool
	 */
	public static function reportSpam($comment)
	{
		if (!$comment || !($comment instanceof Comment_Models_Comment)) {
			throw new Exception('The param is not an instance of Comment_Models_Comment');
		}
		
		$comment->status = Comment_Models_Comment::STATUS_SPAM;
		$result = Comment_Services_Comment::updateStatus($comment);
		
		$akismetApiKey = Core_Services_Config::get('comment', 'akismet_api_key');
		if ($akismetApiKey) {
			$akismetService = new Zend_Service_Akismet($akismetApiKey, Core_Services_Config::get('core', 'url_base'));
			if ($akismetService->verifyKey()) {
				$akismetService->submitSpam(array(
					'user_ip' 			   => $comment->ip,
					'user_agent'		   => $comment->user_agent,
					'comment_type'		   => 'comment',
					'comment_author'	   => $comment->full_name,
					'comment_author_email' => $comment->email,
					'comment_content'	   => $comment->content,
				));
			}
		}
		
		return $result;
	}
	
	/**
	 * Updates given comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return bool
	 */
	public static function update($comment)
	{
		if (!$comment || !($comment instanceof Comment_Models_Comment)) {
			throw new Exception('The param is not an instance of Comment_Models_Comment');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'comment',
							'name'   => 'Comment',
						 ))
						 ->setDbConnection($conn)
						 ->update($comment);
		return true;
	}
	
	/**
	 * Updates comment's status
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return bool
	 */
	public static function updateStatus($comment)
	{
		if (!$comment || !($comment instanceof Comment_Models_Comment)) {
			throw new Exception('The first param is not an instance of Comment_Models_Comment');
		}
		if (!$comment->status || !in_array($comment->status, Comment_Models_Comment::$STATUS)) {
			throw new Exception('Invalid status');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'comment',
							'name'   => 'Comment',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($comment);
						 
		// Execute hooks
		Core_Base_Hook_Registry::getInstance()->executeAction('Comment_Services_Comment_UpdateStatus', array($comment));
		
		return true;
	}
	
	////////// MANAGE VOTES //////////

	/**
	 * Increases the number of vote ups/downs. It is called after user votes a given comment
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return void
	 */
	public static function increaseNumVotes($vote)
	{
		if (!$vote || !($vote instanceof Vote_Models_Vote)) {
			throw new Exception('The param is not an instance of Vote_Models_Vote');
		}
		if ($vote->entity_class != 'Comment_Models_Comment') {
			return;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'comment',
							'name'   => 'Comment',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumVotes($vote);
	}
}
