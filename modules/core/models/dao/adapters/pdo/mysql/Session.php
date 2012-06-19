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

class Core_Models_Dao_Adapters_Pdo_Mysql_Session extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Session
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Session($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Session::delete()
	 */
	public function delete($id)
	{
		$this->_conn->delete($this->_prefix . 'core_session', 
							array(
								'session_id = ?' => $id,
							));
		return true;
	}

	/**
	 * @see Core_Models_Dao_Interface_Session::destroy()
	 */
	public function destroy($time)
	{
		$this->_conn->delete($this->_prefix . 'core_session', 
							array(
								'modified + lifetime < ?' => $time,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Session::getById()
	 */
	public function getById($id)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_session')
					->where('session_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Session($row);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Session::insert()
	 */
	public function insert($session)
	{
		return $this->_conn->insert($this->_prefix . 'core_session', 
									array(
										'session_id' => $session->session_id,
										'data'		 => $session->data,
										'modified'	 => time(),
										'lifetime'	 => $session->lifetime,
									));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Session::update()
	 */
	public function update($session)
	{
		return $this->_conn->update($this->_prefix . 'core_session', 
									array(
										'data'	   => $session->data,
										'modified' => time(),
										'lifetime' => $session->lifetime,
									), 
									array(
										'session_id = ?' => $session->session_id,
									));
	}
}
