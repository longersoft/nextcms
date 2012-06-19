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
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_User
{
	/**
	 * Gets this view helper instance
	 * 
	 * @return Core_View_Helper_User
	 */
	public function user()
	{
		return $this;
	}
	
	/**
	 * Gets the number of users by given conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array())
	{
		Core_Services_Db::connect('master');
		return Core_Services_User::count($criteria);
	}
	
	/**
	 * Returns user instance by given Id
	 * 
	 * @param string $userId The user's Id
	 * @return Core_Models_User
	 */
	public function getById($userId)
	{
		Core_Services_Db::connect('slave');
		return Core_Services_User::getById($userId);
	}
}
