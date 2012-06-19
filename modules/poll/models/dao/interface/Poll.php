<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Poll_Models_Dao_Interface_Poll
{
	/**
	 * Adds new poll
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @return string Id of newly created poll
	 */
	public function add($poll);
	
	/**
	 * Gets the number of polls by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given poll
	 * 
	 * @param Poll_Models_Poll $poll
	 * @return bool
	 */
	public function delete($poll);

	/**
	 * Finds polls by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets poll instance by given Id
	 * 
	 * @param string $pollId Id of poll
	 * @return Poll_Models_Poll|null
	 */
	public function getById($pollId);
	
	/**
	 * Increases the number of choices for each option
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @param array $answers Array of option's Ids
	 * @return void
	 */
	public function increaseNumChoices($poll, $answers);
	
	/**
	 * Updates given poll
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @return void
	 */
	public function update($poll);
}
