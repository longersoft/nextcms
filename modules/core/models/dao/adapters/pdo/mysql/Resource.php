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
 * @version		2011-10-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_Resource extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Resource
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Resource($entity); 
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Resource::add()
	 */
	public function add($resource)
	{
		$this->_conn->insert($this->_prefix . 'core_resource', 
							array(
								'parent_id'		  => $resource->parent_id,
								'description'	  => $resource->description,
								'module_name'	  => $resource->module_name,
								'controller_name' => $resource->controller_name,
								'extension_type'  => $resource->extension_type,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_resource');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Resource::getResources()
	 */
	public function getResources($module = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_resource');
		if ($module) {
			$select->where('module_name = ?', $module);
		}
		$result = $select->order('resource_id DESC')
						 ->query()
						 ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
}
