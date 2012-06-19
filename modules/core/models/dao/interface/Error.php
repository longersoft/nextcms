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

interface Core_Models_Dao_Interface_Error
{
	/**
	 * Add new error
	 * 
	 * @param Core_Models_Error $error The error instance
	 * @return string Id of newly created error
	 */
	public function add($error);
	
	/**
	 * Gets the number of errors by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given error
	 * 
	 * @param Core_Models_Error $error The error instance
	 * @return bool
	 */
	public function delete($error);
	
	/**
	 * Finds errors by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets error instance by given Id
	 * 
	 * @param string $errorId Error's Id
	 * @return Core_Models_Error|null
	 */
	public function getById($errorId);
}
