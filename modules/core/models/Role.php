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

/**
 * Represents an user role
 */
class Core_Models_Role extends Core_Base_Models_Entity 
{
	// The root role.
	// It does not allow to do any tasks to this role, such as lock or delete role.
	// DO NOT CHANGE THIS VALUE
	const ROOT_ROLE = 'admin';
	
	/**
	 * Role's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'role_id' 	  => null,
		'name' 		  => null,
		'description' => null,
		'locked' 	  => null,	// Lock status. Cannot set the role permissions if the it is locked.
		'num_users'   => 0,
	);
	
	/**
	 * Checks if the role is root one
	 * 
	 * @return bool
	 */
	public function isRootRole()
	{
		return $this->name == self::ROOT_ROLE;
	}
}
