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
 * @version		2012-04-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_Role extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Role
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Role($entity); 
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::add()
	 */
	public function add($role)
	{
		$this->_conn->insert($this->_prefix . 'core_role', 
							array(
								'name'		  => $role->name,
								'description' => $role->description,
								'locked'	  => $role->locked,
								'num_users'   => $role->num_users,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_role');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_role', array('num_roles' => 'COUNT(*)'));
		if (isset($criteria['locked']) && $criteria['locked'] != null) {
			$select->where('locked = ?', $criteria['locked']);
		}
		if (isset($criteria['name']) && !empty($criteria['name'])) {
			$select->where("description LIKE '%" . addslashes($criteria['name']) . "%'");
		}
		return $select->limit(1)->query()->fetch()->num_roles;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::delete()
	 */
	public function delete($role)
	{
		$this->_conn->delete($this->_prefix . 'core_role', 
							array(
								'role_id = ?' => $role->role_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_role');
		if (isset($criteria['locked']) && $criteria['locked'] != null) {
			$select->where('locked = ?', $criteria['locked']);
		}
		if (isset($criteria['name']) && !empty($criteria['name'])) {
			$select->where("description LIKE '%" . addslashes($criteria['name']) . "%'");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'role_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::getAclRoles()
	 */
	public function getAclRoles()
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('r' => $this->_prefix . 'core_role'))
					   ->order('role_id')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::getById()
	 */
	public function getById($roleId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_role')
					->where('role_id = ?', $roleId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Role($row);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::rename()
	 */
	public function rename($role)
	{
		$this->_conn->update($this->_prefix . 'core_role',
							array(
								'description' => $role->description,
							),
							array(
								'role_id = ?' => $role->role_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Role::toggleLock()
	 */
	public function toggleLock($role)
	{
		$this->_conn->update($this->_prefix . 'core_role',
							array(
								'locked' => new Zend_Db_Expr('1 - locked'),
							),
							array(
								'role_id = ?' => $role->role_id,
							));
	}
}
