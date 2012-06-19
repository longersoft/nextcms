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

interface Core_Models_Dao_Interface_Role
{
	/**
	 * Adds new role
	 * 
	 * @param Core_Models_Role $role
	 * @return string The Id of newly created role
	 */
	public function add($role);
	
	/**
	 * Gets the number of roles by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes role
	 * 
	 * @param Core_Models_Role $role Instance of role
	 * @return int Number of deleted roles
	 */
	public function delete($role);
	
	/**
	 * Finds roles
	 * 
	 * @param array $criteria An array, can consist of the following keys:
	 * - locked [boolean]
	 * - name [string]
	 * - sort_by
	 * - sort_dir
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * For ACL.
	 * Gets all roles
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public function getAclRoles();
	
	/**
	 * Gets role by given Id
	 * 
	 * @param string $roleId Id of role
	 * @return Core_Models_Role|null
	 */
	public function getById($roleId);
	
	/**
	 * Updates the role name
	 * 
	 * @param Core_Models_Role $role Instance of role
	 * @return void
	 */
	public function rename($role);
	
	/**
	 * Toggles lock status of role
	 * 
	 * @param Core_Models_Role $role Instance of role
	 * @return void
	 */
	public function toggleLock($role);
}
