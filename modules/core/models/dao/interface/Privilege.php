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

interface Core_Models_Dao_Interface_Privilege
{
	/**
	 * Adds new privilege
	 * 
	 * @param Core_Models_Privilege $privilege
	 * @return string The id of new privilege
	 */
	public function add($privilege);
	
	/**
	 * Lists the privileges of given role to a resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param Core_Models_Role $role
	 * @return Core_Base_Models_RecordSet
	 */
	public function getByRole($resource, $role);
	
	/**
	 * Lists the privileges of given user to a resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param Core_Models_User $user
	 * @return Core_Base_Models_RecordSet
	 */
	public function getByUser($resource, $user);
	
	/**
	 * Adds resources, privileges of all modules.
	 * It also removes no longer in use privileges.
	 * 
	 * @param array $modules Array of modules' names
	 * @return void
	 */
	public function sync($modules);
}
