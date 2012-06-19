<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		vote
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Vote_Models_Dao_Interface_Vote
{
	/**
	 * Records a vote
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return string The last vote's Id
	 */
	public function add($vote);
	
	/**
	 * Gets the total number of votes by given criteria
	 * 
	 * @param array $criteria Can contains the following key:
	 * - entity_id
	 * - entity_class
	 * - user_id
	 * - ip
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Gets the total number of up/down votes of given entity 
	 *
	 * @param array $entity Contains the two keys:
	 * - entity_id
	 * - entity_class
	 * @return array An array with two keys of "num_ups" and "num_downs"
	 */
	public function getNumVotes($entity);
}
