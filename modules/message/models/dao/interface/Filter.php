<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	models
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Message_Models_Dao_Interface_Filter
{
	/**
	 * Adds new message filter
	 * 
	 * @param Message_Models_Filter $filter The filter instance
	 * @return string Id of newly created filter
	 */
	public function add($filter);
	
	/**
	 * Deletes given message filter
	 * 
	 * @param Message_Models_Filter $filter The filter instance
	 * @return bool
	 */
	public function delete($filter);
	
	/**
	 * Finds the private message filters
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array());
	
	/**
	 * Gets message filter instance by given Id
	 * 
	 * @param string $filterId Id of message filter
	 * @return Message_Models_Filter|null
	 */
	public function getById($filterId);
}
