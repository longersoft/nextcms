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

class Core_Models_Dao_Adapters_Pdo_Mysql_Config extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Config
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Config($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Config::delete()
	 */
	public function delete($module, $key)
	{
		$this->_conn->delete($this->_prefix . 'core_config',
							array(
								'module = ?'	 => $module,
								'config_key = ?' => $key,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Config::get()
	 */
	public function get($module, $key)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_config', array('config_value'))
					->where('module = ?', $module)
					->where('config_key = ?', $key)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : $row->config_value;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Config::set()
	 */
	public function set($module, $key, $value)
	{
		$query = 'REPLACE INTO ' . $this->_prefix . 'core_config
				  SET module = ?, config_key = ?, config_value = ?';
		$this->_conn->query($query, array($module, $key, $value));
	}
}
