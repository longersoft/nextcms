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

class Core_Models_Dao_Adapters_Pdo_Mysql_Target extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Target
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Target($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Target::getTargets()
	 */
	public function getTargets()
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_hook_target')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
}
