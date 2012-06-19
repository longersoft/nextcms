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

class Core_Models_Dao_Adapters_Pdo_Mysql_Rule extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Rule
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Rule($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Rule::add()
	 */
	public function add($rule)
	{
		$this->_conn->insert($this->_prefix . 'core_rule', 
							array(
								'obj_id'		  => $rule->obj_id,
								'obj_type'		  => $rule->obj_type,
								'allow'			  => $rule->allow,
								'resource_name'	  => $rule->resource_name,
								'module_name'	  => $rule->module_name,
								'controller_name' => $rule->controller_name,
								'action_name'	  => $rule->action_name,
								'extension_type'  => $rule->extension_type,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_rule');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Rule::getAclRules()
	 */
	public function getAclRules()
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_rule', 
							array(
								'role_name'		  => 'CONCAT(obj_type, "_", obj_id)', 
								'allow',
								'resource_name_2' => 'IF(action_name IS NULL, resource_name, CONCAT(extension_type, ":", module_name, ":", controller_name))',
							))
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Rule::setRolePermissions()
	 */
	public function setRolePermissions($role, $privileges)
	{
		// Reset the role's privileges
		$this->_conn->delete($this->_prefix . 'core_rule', 
							array(
								'obj_id = ?'   => $role->role_id,
								'obj_type = ?' => Core_Models_Rule::TYPE_ROLE,
							));
		
		// Set permissions to role
		foreach ($privileges as $privilege) {
			list($action, $controller, $module, $extensionType) = explode('_', $privilege);
			$this->_conn->insert($this->_prefix . 'core_rule',
								array(
									'obj_id'		  => $role->role_id,
									'obj_type'		  => Core_Models_Rule::TYPE_ROLE,
									'allow'			  => 1,
									'resource_name'	  => $extensionType . ':' . $module . ':' . $controller,
									'module_name'	  => $module,
									'controller_name' => $controller,
									'action_name'	  => $action,
									'extension_type'  => $extensionType,
								));
		}
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Rule::setUserPermissions()
	 */
	public function setUserPermissions($user, $privileges)
	{
		// Reset user's permissions
		$this->_conn->delete($this->_prefix . 'core_rule',
							array(
								'obj_id = ?'   => $user->user_id,
								'obj_type = ?' => Core_Models_Rule::TYPE_USER,
							));
		
		// Set permissions to user
		foreach ($privileges as $privilege) {
			list($action, $controller, $module, $extensionType) = explode('_', $privilege); 
			$this->_conn->insert($this->_prefix . 'core_rule',
								array(
									'obj_id'		  => $user->user_id,
									'obj_type'		  => Core_Models_Rule::TYPE_USER,
									'action_name'	  => $action,
									'allow'			  => 1,
									'resource_name'	  => $extensionType . ':' . $module . ':' . $controller,
									'module_name'	  => $module,
									'controller_name' => $controller,
									'extension_type'  => $extensionType,
								));
		}
	}
}
