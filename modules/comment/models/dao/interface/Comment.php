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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Comment_Models_Dao_Interface_Comment
{
	/**
	 * Adds new comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return string Id of newly created comment
	 */
	public function add($comment);
	
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
	public function count($criteria = array());
	
	/**
	 * Gets the number of threads
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function countThreads($criteria = array());
	
	/**
	 * Deletes given comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return void
	 */
	public function delete($comment);

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
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Finds the latest comments which belong to separate threads
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function findThreads($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets comment by given Id
	 * 
	 * @param string $commentId
	 * @return Comment_Models_Comment|null
	 */
	public function getById($commentId);
	
	/**
	 * Updates given comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return void
	 */
	public function update($comment);
	
	/**
	 * Updates comment's status
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return void
	 */
	public function updateStatus($comment);
	
	////////// MANAGE VOTES //////////

	/**
	 * Increases the number of vote ups/downs. It is called after user votes a given comment
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return void
	 */
	public function increaseNumVotes($vote);
}
