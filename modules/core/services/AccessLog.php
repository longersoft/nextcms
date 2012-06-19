<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_AccessLog
{
	/**
	 * Adds access log
	 * 
	 * @param Core_Models_AccessLog $accessLog The access log instance
	 * @return string Id of newly created log
	 */
	public static function add($accessLog)
	{
		if ($accessLog == null || !($accessLog instanceof Core_Models_AccessLog)) {
			throw new Exception('The param is not an instance of Core_Models_AccessLog');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'AccessLog',
								))
								->setDbConnection($conn)
								->add($accessLog);
	}
	
	/**
	 * Gets the number of access logs by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'AccessLog',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given access log
	 * 
	 * @param Core_Models_AccessLog $accessLog The access log instance
	 * @return bool
	 */
	public static function delete($accessLog)
	{
		if (!$accessLog || !($accessLog instanceof Core_Models_AccessLog)) {
			throw new Exception('The param is not an instance of Core_Models_AccessLog');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'AccessLog',
						 ))
						 ->setDbConnection($conn)
						 ->delete($accessLog);
		return true;
	}
	
	/**
	 * Finds access logs by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'AccessLog',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets access log instance by given Id
	 * 
	 * @param string $logId Log's Id
	 * @return Core_Models_AccessLog|null
	 */
	public static function getById($logId)
	{
		if ($logId == null || empty($logId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'AccessLog',
								))
								->setDbConnection($conn)
								->getById($logId);
	}
}
