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

class Core_Models_Dao_Adapters_Pdo_Mysql_Privilege extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Privilege
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Privilege($entity); 
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Privilege::add()
	 */
	public function add($privilege)
	{
		$this->_conn->insert($this->_prefix . 'core_privilege', 
							array(
								'description' 	  => $privilege->description,
								'module_name' 	  => $privilege->module_name,
								'controller_name' => $privilege->controller_name,
								'action_name'     => $privilege->action_name,
								'extension_type'  => $privilege->extension_type,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_privilege');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Privilege::getByRole()
	 */
	public function getByRole($resource, $role)
	{
		$resourceId = $resource->extension_type . ':' . $resource->module_name . ':' . $resource->controller_name;
		$result		= $this->_conn
						   ->select()
						   ->from(array('p' => $this->_prefix . 'core_privilege'), array('privilege_id', 'action_name', 'controller_name', 'module_name', 'extension_type', 'description'))
						   ->joinLeft(array('r' => $this->_prefix . 'core_rule'),
									'r.obj_type = ?
									AND r.obj_id = ?
									AND 
									(
										(r.action_name IS NULL AND r.resource_name IS NULL)
										OR 
										(r.action_name IS NULL AND (r.resource_name = ?))
										OR 
										((r.resource_name = ?) AND (r.action_name = p.action_name))
									)',
									array('allow'))
						   ->where('p.module_name = ?', $resource->module_name)
						   ->where('p.controller_name = ?', $resource->controller_name)
						   ->where('p.extension_type = ?', $resource->extension_type)
						   ->bind(array(
								Core_Models_Rule::TYPE_ROLE,
								$role->role_id,
								$resourceId,
								$resourceId,
						   ))
						   ->query()
						   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Privilege::getByUser()
	 */
	public function getByUser($resource, $user)
	{
		$resourceId = $resource->extension_type . ':' . $resource->module_name . ':' . $resource->controller_name;
		$result		= $this->_conn
						   ->select()
						   ->from(array('p' => $this->_prefix . 'core_privilege'), array('privilege_id', 'action_name', 'controller_name', 'module_name', 'extension_type', 'description'))
						   ->joinLeft(array('r' => $this->_prefix . 'core_rule'),
									'r.obj_type = ?
									AND r.obj_id = ?
									AND
									(
										(r.action_name IS NULL AND r.resource_name IS NULL)
										OR
										(r.action_name IS NULL AND (r.resource_name = ?))
										OR
										((r.resource_name = ?) AND (r.action_name = p.action_name))
									)',
									array('allow'))
						   ->where('p.module_name = ?', $resource->module_name)
						   ->where('p.controller_name = ?', $resource->controller_name)
						   ->where('p.extension_type = ?', $resource->extension_type)
						   ->bind(array(
								Core_Models_Rule::TYPE_USER,
								$user->user_id,
								$resourceId,
								$resourceId,
						   ))
						   ->query()
						   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Privilege::sync()
	 */
	public function sync($modules)
	{
		$moduleStrArray = array();
		foreach ($modules as $module) {
			$moduleStrArray[] = $this->_conn->quote($module);
		}
		
		// Remove all resources, privileges, rules of module which is uninstalled
		$this->_conn->delete($this->_prefix . 'core_resource');
		$this->_conn->delete($this->_prefix . 'core_privilege');
		$this->_conn->delete($this->_prefix . 'core_rule',
							array(
								'module_name IS NOT NULL',
								'module_name NOT IN (?)' => new Zend_Db_Expr(implode(',', $moduleStrArray)),
							));
		foreach ($modules as $module) {
			$privileges = Core_Services_Privilege::getPrivileges($module);
			foreach (array('module', 'hook', 'plugin', 'task', 'widget') as $type) {
				foreach ($privileges[$type] as $priv) {
					$this->_sync($priv['resource'], $priv['privileges']);
				}
			}
		}
	}
	
	/**
	 * @param Core_Models_Resource $resource The resource instance
	 * @param array $privileges Array of privileges
	 * @return void
	 */
	private function _sync($resource, $privileges)
	{
		// Add resource
		$this->_conn->insert($this->_prefix . 'core_resource', 
							array(
								'parent_id' 	  => $resource->parent_id,
								'description' 	  => $resource->description,
								'module_name' 	  => $resource->module_name,
								'controller_name' => $resource->controller_name,
								'extension_type'  => $resource->extension_type,
							));
		
		$actions = array();
		foreach ($privileges as $privilege) {
			// Add privilege
			$this->add($privilege);
			
			$actions[] = $this->_conn->quote($privilege->action_name);
		}
		
		// Remove all no longer in use rules
		if (count($actions) > 0) {
			$this->_conn->delete($this->_prefix . 'core_rule',
								array(
									'module_name = ?'		 => $resource->module_name,
									'controller_name = ?'	 => $resource->controller_name,
									'extension_type = ?'	 => $resource->extension_type,
									'action_name NOT IN (?)' => new Zend_Db_Expr(implode(',', $actions)),
								));
		}
	}
}
