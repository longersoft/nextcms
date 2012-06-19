<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface File_Models_Dao_Interface_Connection
{
	/**
	 * Adds new connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return string Id of newly added connection
	 */
	public function add($connection);
	
	/**
	 * Deletes a connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return void
	 */
	public function delete($connection);
	
	/**
	 * Gets the list of connections
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public function find();
	
	/**
	 * Gets the connection instance by given id
	 * 
	 * @param string $connectionId
	 * @return File_Models_Connection|null
	 */
	public function getById($connectionId);
	
	/**
	 * Renames given connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return void
	 */
	public function rename($connection);
	
	/**
	 * Updates given connection
	 * 
	 * @param File_Models_Connection $connection
	 * @return bool
	 */
	public function update($connection);
}
