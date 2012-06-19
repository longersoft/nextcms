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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_Services_Poll
{
	/**
	 * Adds new poll
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @return string Id of newly created poll
	 */
	public static function add($poll)
	{
		if (!$poll || !($poll instanceof Poll_Models_Poll)) {
			throw new Exception('The param is not an instance of Poll_Models_Poll');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'poll',
									'name'   => 'Poll',
								))
								->setDbConnection($conn)
								->add($poll);
	}
	
	/**
	 * Gets the number of polls by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'poll',
									'name'   => 'Poll',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given poll
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @return bool
	 */
	public static function delete($poll)
	{
		if (!$poll || !($poll instanceof Poll_Models_Poll)) {
			throw new Exception('The param is not an instance of Poll_Models_Poll');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'poll',
							'name'   => 'Poll',
						 ))
						 ->setDbConnection($conn)
						 ->delete($poll);
		return true;
	}

	/**
	 * Finds polls by given collection of conditions
	 * 
	 * @param array $criteria
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
									'module' => 'poll',
									'name'   => 'Poll',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets poll instance by given Id
	 * 
	 * @param string $pollId Id of poll
	 * @return Poll_Models_Poll|null
	 */
	public static function getById($pollId)
	{
		if ($pollId == null || empty($pollId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'poll',
									'name'   => 'Poll',
								))
								->setDbConnection($conn)
								->getById($pollId);
	}
	
	/**
	 * Gets poll options
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getOptions($poll)
	{
		if (!$poll || !($poll instanceof Poll_Models_Poll)) {
			throw new Exception('The param is not an instance of Poll_Models_Poll');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'poll',
									'name'   => 'Option',
								))
								->setDbConnection($conn)
								->getOptions($poll);
	}
	
	/**
	 * Increases the number of choices for each option
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @param array $answers Array of option's Ids
	 * @return void
	 */
	public static function increaseNumChoices($poll, $answers)
	{
		if (!$poll || !($poll instanceof Poll_Models_Poll)) {
			throw new Exception('The param is not an instance of Poll_Models_Poll');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'poll',
							'name'   => 'Poll',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumChoices($poll, $answers);
	}
	
	/**
	 * Updates given poll
	 * 
	 * @param Poll_Models_Poll $poll The poll instance
	 * @return bool
	 */
	public static function update($poll)
	{
		if (!$poll || !($poll instanceof Poll_Models_Poll)) {
			throw new Exception('The param is not an instance of Poll_Models_Poll');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'poll',
							'name'   => 'Poll',
						 ))
						 ->setDbConnection($conn)
						 ->update($poll);
		return true;
	}
}
