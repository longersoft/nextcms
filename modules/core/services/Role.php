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
 * @version		2011-10-31
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Role
{
	/**
	 * Adds new role
	 * 
	 * @param Core_Models_Role $role The role instance
	 * @return string Id of newly created role
	 */
	public static function add($role)
	{
		if ($role->name == null) {
			// Generate unique role's name
			$role->name = uniqid();
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Role',
								))
								->setDbConnection($conn)
								->add($role);
	}
	
	/**
	 * Gets the number of roles by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Role',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes role
	 * 
	 * @param Core_Models_Role $role The role instance
	 * @return bool
	 */
	public static function delete($role)
	{
		switch (true) {
			case ($role == null || !($role instanceof Core_Models_Role)):
				throw new Exception('The param is not an instance of Core_Models_Role');
				break;
			case ($role->isRootRole()):
				throw new Exception('Cannot delete the root role');
				break;
			case ((int) $role->num_users > 0):
				throw new Exception('Cannot delete the role having users');
				break;
			case ($role->locked == '1'):
				throw new Exception('Cannot delete the locked role');
				break;
			default:
				$conn = Core_Services_Db::getConnection();
				Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'Role',
								 ))
								 ->setDbConnection($conn)
								 ->delete($role);
				return true;
		}

		return false;
	}
	
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
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Role',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getAclRoles()
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'	 => 'Role',
								))
								->setDbConnection($conn)
								->getAclRoles();
	}
	
	/**
	 * Gets role instance by given Id
	 * 
	 * @param string $roleId The role's Id
	 * @return Core_Models_Role|null
	 */
	public static function getById($roleId)
	{
		if ($roleId == null || empty($roleId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Role',
								))
								->setDbConnection($conn)
								->getById($roleId);
	}
	
	/**
	 * Updates name of role
	 * 
	 * @param Core_Models_Role $role The role instance
	 * @return bool
	 */
	public static function rename($role)
	{
		switch (true) {
			case ($role == null || !($role instanceof Core_Models_Role)):
				throw new Exception('The param is not an instance of Core_Models_Role');
				break;
			case ($role->role_id == null || empty($role->role_id)):
				return false;
			case ($role->description == null || empty($role->description)):
				return false;
			default:
				$conn = Core_Services_Db::getConnection();
				Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Role',
								 ))
								 ->setDbConnection($conn)
								 ->rename($role);
				return true;
		}
	}
	
	/**
	 * Toggles the lock status of role
	 * 
	 * @param Core_Models_Role $role The role instance
	 * @return bool
	 */
	public static function toggleLock($role)
	{
		if ($role == null || !($role instanceof Core_Models_Role)) {
			throw new Exception('The param is not an instance of Core_Models_Role');
		}
		if ($role->isRootRole()) {
			return false;
		}
		
 		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Role',
								 ))
								 ->setDbConnection($conn)
								 ->toggleLock($role);
		return true;
	}
}
