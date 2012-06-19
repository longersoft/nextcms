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
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_Dashboard
{
	/**
	 * Adds new dashboard
	 * 
	 * @param Core_Models_Dashboard $dashboard
	 * @return string Id of newly created dashboard
	 */
	public function add($dashboard);
	
	/**
	 * Gets user's dashboard
	 * 
	 * @param Core_Models_User $user
	 * @return Core_Models_Dashboard|null
	 */
	public function getByUser($user);
	
	/**
	 * Updates dashboard's layout
	 * 
	 * @param Core_Models_Dashboard $dashboard
	 * @return void
	 */
	public function update($dashboard);
}
