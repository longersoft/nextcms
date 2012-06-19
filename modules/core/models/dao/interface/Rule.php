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
 * @version		2011-10-31
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_Rule
{
	/**
	 * Adds new rule
	 * 
	 * @param Core_Models_Rule $rule
	 * @return string The id of newly created rule
	 */
	public function add($rule);
	
	/**
	 * For ACL.
	 * Gets all rules
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public function getAclRules();
	
	/**
	 * Sets role permissions
	 * 
	 * @param Core_Models_Role $role The role instance
	 * @param array $privileges Array of privileges. Each item is a string defined as actioName_controllerName_moduleName
	 * @return void
	 */
	public function setRolePermissions($role, $privileges);
	
	/**
	 * Sets user permissions
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param array $privileges Array of privileges. Each item is a string defined as actioName_controllerName_moduleName
	 * @return void
	 */
	public function setUserPermissions($user, $privileges);
}
