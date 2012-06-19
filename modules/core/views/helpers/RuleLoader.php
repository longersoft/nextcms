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

class Core_View_Helper_RuleLoader
{
	/**
	 * Gets the view helper instance
	 * 
	 * @return Core_View_Helper_RuleLoader
	 */
	public function ruleLoader()
	{
		Core_Services_Db::connect('master');
		return $this;
	}
	
	/**
	 * Gets the list of resource of given module
	 * 
	 * @param string $module
	 * @return Core_Base_Models_RecordSet
	 */
	public function getResources($module)
	{
		return Core_Services_Resource::getResources($module);
	}
	
	/**
	 * Lists the privileges of given role to a resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param Core_Models_Role $role
	 * @return Core_Base_Models_RecordSet
	 */
	public function getByRole($resource, $role)
	{
		return Core_Services_Privilege::getByRole($resource, $role);
	}
	
	/**
	 * Lists the privileges of given user to a resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param Core_Models_User $user
	 * @return Core_Base_Models_RecordSet
	 */
	public function getByUser($resource, $user)
	{
		return Core_Services_Privilege::getByUser($resource, $user);
	}
}
