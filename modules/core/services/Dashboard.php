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

class Core_Services_Dashboard
{
	/**
	 * Adds new dashboard
	 * 
	 * @param Core_Models_Dashboard $dashboard
	 * @return string Id of newly created dashboard
	 */
	public static function add($dashboard)
	{
		if (!$dashboard || !($dashboard instanceof Core_Models_Dashboard)) {
			throw new Exception('The input is not an instance of Core_Models_Dashboard');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Dashboard',
								))
								->setDbConnection($conn)
								->add($dashboard);
	}
	
	/**
	 * Gets user's dashboard
	 * 
	 * @param Core_Models_User $user
	 * @return Core_Models_Dashboard|null
	 */
	public static function getByUser($user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The input is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Dashboard',
								))
								->setDbConnection($conn)
								->getByUser($user);
	}
	
	/**
	 * Updates dashboard's layout
	 * 
	 * @param Core_Models_Dashboard $dashboard
	 * @return bool
	 */
	public static function update($dashboard)
	{
		if (!$dashboard || !($dashboard instanceof Core_Models_Dashboard)) {
			throw new Exception('The input is not an instance of Core_Models_Dashboard');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Dashboard',
						 ))
						 ->setDbConnection($conn)
						 ->update($dashboard);
		return true;
	}
}
