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

class Core_Models_Dao_Adapters_Pdo_Mysql_Dashboard extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Dashboard
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Dashboard($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Dashboard::add()
	 */
	public function add($dashboard)
	{
		$this->_conn->insert($this->_prefix . 'core_dashboard',
							array(
								'user_id' => $dashboard->user_id,
								'layout'  => $dashboard->layout,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_dashboard');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Dashboard::getByUser()
	 */
	public function getByUser($user)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_dashboard')
					->where('user_id = ?', $user->user_id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Dashboard($row);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Dashboard::update()
	 */
	public function update($dashboard)
	{
		$this->_conn->update($this->_prefix . 'core_dashboard',
							array(
								'layout' => $dashboard->layout,
							),
							array(
								'dashboard_id = ?' => $dashboard->dashboard_id,
							));
	}
}
