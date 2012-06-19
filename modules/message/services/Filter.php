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
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Services_Filter
{
	/**
	 * Adds new message filter
	 * 
	 * @param Message_Models_Filter $filter The filter instance
	 * @return string Id of newly created filter
	 */
	public static function add($filter)
	{
		if ($filter == null || !($filter instanceof Message_Models_Filter)) {
			throw new Exception('The param is not an instance of Message_Models_Filter');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Filter',
								))
								->setDbConnection($conn)
								->add($filter);
	}
	
	/**
	 * Deletes given message filter
	 * 
	 * @param Message_Models_Filter $filter The filter instance
	 * @return bool
	 */
	public static function delete($filter)
	{
		if (!$filter || !($filter instanceof Message_Models_Filter)) {
			throw new Exception('The param is not an instance of Message_Models_Filter');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'message',
							'name'   => 'Filter',
						 ))
						 ->setDbConnection($conn)
						 ->delete($filter);
		return true;
	}
	
	/**
	 * Finds the private message filters
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Filter',
								))
								->setDbConnection($conn)
								->find($criteria);
	}
	
	/**
	 * Gets message filter instance by given Id
	 * 
	 * @param string $filterId Id of message filter
	 * @return Message_Models_Filter|null
	 */
	public static function getById($filterId)
	{
		if ($filterId == null || empty($filterId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'message',
									'name'   => 'Filter',
								))
								->setDbConnection($conn)
								->getById($filterId);
	}
}
