<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Menu_Models_Dao_Interface_Menu
{
	/**
	 * Adds new menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return string Id of newly created menu
	 */
	public function add($menu);
	
	/**
	 * Gets the number of menus by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return void
	 */
	public function delete($menu);
	
	/**
	 * Finds menus by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets menu instance by given Id
	 * 
	 * @param string $menuId Menu's Id
	 * @return Menu_Models_Menu|null
	 */
	public function getById($menuId);
	
	/**
	 * Updates given menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return void
	 */
	public function update($menu);
}
