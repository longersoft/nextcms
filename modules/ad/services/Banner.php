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
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_Services_Banner
{
	/**
	 * Adds new banner
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return string Id of newly created banner
	 */
	public static function add($banner)
	{
		if ($banner == null || !($banner instanceof Ad_Models_Banner)) {
			throw new Exception('The param is not an instance of Ad_Models_Banner');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->add($banner);
	}
	
	/**
	 * Adds association between banner, zone and page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @param array $banners Each item is an array containing two keys:
	 * - banner_id: The banner's Id
	 * - zone_id: The zone's Id
	 * - ordering: The index of banner in zone
	 * @return bool
	 */
	public static function addAssociationPage($page, $banners)
	{
		if ($page == null || !($page instanceof Core_Models_Page)) {
			throw new Exception('The first param is not an instance of Core_Models_Page');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'ad',
							'name'   => 'Banner',
						 ))
						 ->setDbConnection($conn)
						 ->addAssociationPage($page, $banners);
		return true;
	}
	
	/**
	 * Gets the number of banners by given criteria
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
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given banner
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return bool
	 */
	public static function delete($banner)
	{
		if ($banner == null || !($banner instanceof Ad_Models_Banner)) {
			throw new Exception('The param is not an instance of Ad_Models_Banner');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'ad',
							'name'   => 'Banner',
						 ))
						 ->setDbConnection($conn)
						 ->delete($banner);
		return true;
	}
	
	/**
	 * Finds banners by given criteria
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
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets the list of banners on given page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getAssociationBanners($page)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->getAssociationBanners($page);
	}
	
	/**
	 * Gets the list of banners that are placed in given zone of certain page
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @param Core_Models_Page $page The page instance. It has to contain 3 properties:
	 * - route
	 * - language
	 * - template
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getBannersInZone($zone, $page = null)
	{
		if ($zone == null || !($zone instanceof Ad_Models_Zone)) {
			throw new Exception('The param is not an instance of Ad_Models_Zone');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->getBannersInZone($zone, $page);
	}
	
	/**
	 * Gets banner by given Id
	 * 
	 * @param string $bannerId Id of the banner
	 * @return Ad_Models_Banner
	 */
	public static function getById($bannerId)
	{
		if (!$bannerId) {
			throw new Exception("The banner's Id is required");
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->getById($bannerId);
	}
	
	/**
	 * Gets the list of static links which banner is placed on
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return array
	 */
	public static function getStaticLinks($banner)
	{
		if ($banner == null || !($banner instanceof Ad_Models_Banner)) {
			throw new Exception('The param is not an instance of Ad_Models_Banner');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'ad',
									'name'   => 'Banner',
								))
								->setDbConnection($conn)
								->getStaticLinks($banner);
	}
	
	/**
	 * Updates given banner
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return bool
	 */
	public static function update($banner)
	{
		if (!$banner || !($banner instanceof Ad_Models_Banner)) {
			throw new Exception('The param is not an instance of Ad_Models_Banner');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'ad',
							'name'   => 'Banner',
						 ))
						 ->setDbConnection($conn)
						 ->update($banner);
		return true;
	}
	
	/**
	 * Updates banner's status
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return bool
	 */
	public static function updateStatus($banner)
	{
		if (!$banner || !($banner instanceof Ad_Models_Banner)) {
			throw new Exception('The param is not an instance of Ad_Models_Banner');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'ad',
							'name'   => 'Banner',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($banner);
		return true;
	}
}
