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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Ad_Models_Dao_Interface_Banner
{
	/**
	 * Adds new banner
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return string Id of newly created banner
	 */
	public function add($banner);
	
	/**
	 * Adds association between banner, zone and page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @param array $banners Each item is an array containing two keys:
	 * - banner_id: The banner's Id
	 * - zone_id: The zone's Id
	 * - ordering: The index of banner in zone
	 * @return void
	 */
	public function addAssociationPage($page, $banners);
	
	/**
	 * Gets the number of banners by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given banner
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return void
	 */
	public function delete($banner);
	
	/**
	 * Finds banners by given criteria
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets the list of banners on given page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return Core_Base_Models_RecordSet
	 */
	public function getAssociationBanners($page);
	
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
	public function getBannersInZone($zone, $page = null);
	
	/**
	 * Gets banner by given Id
	 * 
	 * @param string $bannerId Id of the banner
	 * @return Ad_Models_Banner
	 */
	public function getById($bannerId);
	
	/**
	 * Gets the list of static links which banner is placed on
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return array
	 */
	public function getStaticLinks($banner);
	
	/**
	 * Updates given banner
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return void
	 */
	public function update($banner);
	
	/**
	 * Updates banner's status
	 * 
	 * @param Ad_Models_Banner $banner The banner instance
	 * @return void
	 */
	public function updateStatus($banner);
}
