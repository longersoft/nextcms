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

interface Menu_Models_Dao_Interface_Item
{
	/**
	 * Gets items tree of given menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return Core_Base_Models_RecordSet
	 */
	public function getItemsTree($menu);
}
