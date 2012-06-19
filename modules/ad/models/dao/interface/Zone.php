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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Ad_Models_Dao_Interface_Zone
{
	/**
	 * Adds new zone
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @return string Id of newly created zone
	 */
	public function add($zone);
	
	/**
	 * Deletes given zone
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @return void
	 */
	public function delete($zone);
	
	/**
	 * Finds zones
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public function find();	
	
	/**
	 * Gets zone by given Id
	 * 
	 * @param string $zoneId The zone's Id
	 * @return Ad_Models_Zone
	 */
	public function getById($zoneId);
	
	/**
	 * Updates given zone
	 * 
	 * @param Ad_Models_Zone $zone The zone instance
	 * @return void
	 */
	public function update($zone);
}
