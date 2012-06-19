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
 * @version		2012-04-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Acl extends Zend_Acl
{
	/**
	 * The ACL instance
	 * 
	 * @var Core_Services_Acl
	 */
	private static $_instance = null;

	/**
	 * Singleton method to get the instance of ACL
	 * 
	 * @return Core_Services_Acl
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Private constructor
	 * 
	 * @return void
	 */
	private function __construct()
	{
		$this->_buildResources();
		$this->_buildRoles();
		$this->_buildRules();
	}
	
	/**
	 * Creates the resources
	 * 
	 * @return void
	 */
	private function _buildResources()
	{
		$resources = Core_Services_Resource::getResources(null, false);
		
		if (0 == count($resources)) {
			return;
		}
		
		$allResources = array();
		// Map resource id to its name
		$map = array();
		foreach ($resources as $row) {
			$allResources[] 		= $row->resource_id;
			$map[$row->resource_id] = self::_buildResourceIdentity($row);
		}
		foreach ($resources as $row) {
			if ($row->parent_id !== null && !empty($row->parent_id) && !in_array($row->parent_id, $allResources)) {
				throw new Zend_Acl_Exception('Resource id "' . $row->parent_id . '" does not exist');
			}
		}

		$numResources = count($resources);
		$i = 0;
		while ($numResources > $i) {
			foreach ($resources as $row) {
				// Check if parent resource (if any) exists
				// Only add if this resource hasn't yet been added and its parent is known, if any
				$resId = self::_buildResourceIdentity($row);

				$has = false;
				if ($row->parent_id != null) {
					$parentName = isset($map[$row->parent_id]) ? $map[$row->parent_id] : null;
					if (null == $parentName) {
						$has = false;
					} else {
						$has = $this->has($parentName);
					}
				}

				if (!$this->has($resId)) {
					if ($has) {
						$this->addResource(new Zend_Acl_Resource($resId), $parentResId);
					} else {
						$this->addResource(new Zend_Acl_Resource($resId));
					}
					$i++;
				}
			}
		}
	}
	
	/**
	 * Creates roles
	 * 
	 * @return void
	 */
	private function _buildRoles() 
	{
		$roles = Core_Services_Role::getAclRoles();
		if ($roles && count($roles) > 0) {
			foreach ($roles as $role) {
				$roleName = self::_buildRoleIdentity($role);
				
				if (!$this->hasRole($roleName)) {
					$this->addRole(new Zend_Acl_Role($roleName));
				}
			}
		}
	}
	
	/**
	 * Creates rules
	 * 
	 * @return void
	 */
	private function _buildRules() 
	{
		$rules = Core_Services_Rule::getAclRules();
		if ($rules && count($rules) > 0) {
			foreach ($rules as $row) {
				if (!$this->hasRole($row->role_name)) {
					$this->addRole(new Zend_Acl_Role($row->role_name));
				}
				if ($row->allow == true) {
					$this->allow($row->role_name, $row->resource_name_2, $row->action_name);
				} else {
					$this->deny($row->role_name, $row->resource_name_2, $row->action_name);
				}
			}
		}
	}
	
	/**
	 * Determines if the current logged in user has the permission to access given action
	 * 
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param string $extensionType
	 * @return bool
	 */
	public function isUserOrRoleAllowed($module, $controller, $action = null, $extensionType = 'module')
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			return false;
		}
		if ($action != null) {
			$action = strtolower($action);
		}
		
		// Build the resource identity as returned by _buildResourceIdentity() method
		$resource = strtolower($extensionType . ':' . $module . ':' . $controller);
		
		$user = Zend_Auth::getInstance()->getIdentity();
		
		// Return FALSE if the resource don't exist
		if (!$this->has($resource)) {
			return false;
		}
		
		$roleId = self::_buildRoleIdentity($user->role);
		$userId = self::_buildRoleIdentity($user);
		if (($this->hasRole($roleId) && $this->isAllowed($roleId, $resource, $action))			// User belongs to role which has the permission
				|| ($this->hasRole($userId) && $this->isAllowed($userId, $resource, $action)))	// User has the permission
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Builds the resource name
	 * 
	 * @param Core_Models_Resource $resource The resource instance
	 * @return string
	 */
	private static function _buildResourceIdentity($resource)
	{
		return strtolower($resource->extension_type . ':' . $resource->module_name . ':' . $resource->controller_name);
	}

	/**
	 * Generates the role name which will be added to ACL based on given role
	 * 
	 * @param Core_Models_User|Core_Models_Role $userOrRole The user/role instance
	 * @return string
	 */
	private static function _buildRoleIdentity($userOrRole)
	{
		switch (true) {
			case ($userOrRole instanceof Core_Models_User):
				return Core_Models_Rule::TYPE_USER . '_' . $userOrRole->user_id;
			case ($userOrRole instanceof Core_Models_Role):
				return Core_Models_Rule::TYPE_ROLE . '_' . $userOrRole->role_id;
		}
	}
}
