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

class Core_Services_Error
{
	/**
	 * Add new error
	 * 
	 * @param Core_Models_Error $error The error instance
	 * @return string Id of newly created error
	 */
	public static function add($error)
	{
		if ($error == null || !($error instanceof Core_Models_Error)) {
			throw new Exception('The param is not an instance of Core_Models_Error');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Error',
								))
								->setDbConnection($conn)
								->add($error);
	}
	
	/**
	 * Gets the number of errors by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Error',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given error
	 * 
	 * @param Core_Models_Error $error The error instance
	 * @return bool
	 */
	public static function delete($error)
	{
		if (!$error || !($error instanceof Core_Models_Error)) {
			throw new Exception('The param is not an instance of Core_Models_Error');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Error',
						 ))
						 ->setDbConnection($conn)
						 ->delete($error);
		return true;
	}
	
	/**
	 * Finds errors by given collection of conditions
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
									'name'   => 'Error',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets error instance by given Id
	 * 
	 * @param string $errorId Error's Id
	 * @return Core_Models_Error|null
	 */
	public static function getById($errorId)
	{
		if ($errorId == null || empty($errorId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Error',
								))
								->setDbConnection($conn)
								->getById($errorId);
	}
}
