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

interface Core_Models_Dao_Interface_AccessLog
{
	/**
	 * Adds access log
	 * 
	 * @param Core_Models_AccessLog $accessLog The access log instance
	 * @return string Id of newly created log
	 */
	public function add($accessLog);
	
	/**
	 * Gets the number of access logs by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given access log
	 * 
	 * @param Core_Models_AccessLog $accessLog The access log instance
	 * @return void
	 */
	public function delete($accessLog);
	
	/**
	 * Finds access logs by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets access log instance by given Id
	 * 
	 * @param string $logId Log's Id
	 * @return Core_Models_AccessLog|null
	 */
	public function getById($logId);
}
