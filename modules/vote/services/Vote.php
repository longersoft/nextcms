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
 * @subpackage	services
 * @since		1.0
 * @version		2012-03-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Vote_Services_Vote
{
	/**
	 * Records a vote
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return string The last vote's Id
	 */
	public static function add($vote)
	{
		if (!$vote || !($vote instanceof Vote_Models_Vote)) {
			throw new Exception('The param is not an instance of Vote_Models_Vote');
		}
		$conn	= Core_Services_Db::getConnection();
		$voteId = Core_Services_Dao::factory(array(
										'module' => 'vote',
										'name'   => 'Vote',
								   ))
								   ->setDbConnection($conn)
								   ->add($vote);

		// Execute hooks
		Core_Base_Hook_Registry::getInstance()->executeAction('Vote_Services_Vote_Add', array($vote));
								
		return $voteId;
	}
	
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
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'vote',
									'name'   => 'Vote',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Gets the total number of up/down votes of given entity 
	 *
	 * @param array $entity Contains the two keys:
	 * - entity_id
	 * - entity_class
	 * @return array An array with two keys of "num_ups" and "num_downs"
	 */
	public static function getNumVotes($entity)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'vote',
									'name'   => 'Vote',
								))
								->setDbConnection($conn)
								->count($entity);
	}
}
