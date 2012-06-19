<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_Models_Dao_Adapters_Pdo_Mysql_Option extends Core_Base_Models_Dao
	implements Poll_Models_Dao_Interface_Option
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Poll_Models_Option($entity);
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Option::getOptions()
	 */
	public function getOptions($poll)
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'poll_option')
					   ->where('poll_id = ?', $poll->poll_id)
					   ->order('ordering ASC')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
}
