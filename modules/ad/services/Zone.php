<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_Services_Zone
{
	/**
	 * Adds new zone
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @return string Id of newly created zone
	 */
	public static function add($zone)
	{
		if ($zone == null || !($zone instanceof Ad_Models_Zone)) {
			throw new Exception('The param is not an instance of Ad_Models_Zone');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Zone',
								))
								->setDbConnection($conn)
								->add($zone);
	}
	
	/**
	 * Deletes given zone
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @return bool
	 */
	public static function delete($zone)
	{
		if ($zone == null || !($zone instanceof Ad_Models_Zone)) {
			throw new Exception('The param is not an instance of Ad_Models_Zone');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'ad',
							'name'   => 'Zone',
						 ))
						 ->setDbConnection($conn)
						 ->delete($zone);
		return true;
	}
	
	/**
	 * Finds zones
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find()
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Zone',
								))
								->setDbConnection($conn)
								->find();
	}
	
	/**
	 * Gets zone by given Id
	 * 
	 * @param string $zoneId The zone's Id
	 * @return Ad_Models_Zone
	 */
	public static function getById($zoneId)
	{
		if ($zoneId == null || empty($zoneId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Zone',
								))
								->setDbConnection($conn)
								->getById($zoneId);
	}	
	
	/**
	 * Updates given zone
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @return bool
	 */
	public static function update($zone)
	{
		if ($zone == null || !($zone instanceof Ad_Models_Zone)) {
			throw new Exception('The param is not an instance of Ad_Models_Zone');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'ad',
							'name'   => 'Zone',
						 ))
						 ->setDbConnection($conn)
						 ->update($zone);
		return true;
	}
}
