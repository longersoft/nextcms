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

interface Core_Models_Dao_Interface_Session
{
	/**
	 * Deletes all session data by given Id
	 * 
	 * @param string $id Id of session
	 * @return bool
	 */
	public function delete($id);

	/**
	 * Destroys all timeout session
	 * 
	 * @param int $time The timestamp
	 */
	public function destroy($time);
	
	/**
	 * Gets session by given Id
	 * 
	 * @param int $id Id of session
	 * @return Core_Models_Session
	 */
	public function getById($id);
	
	/**
	 * Creates new session
	 * 
	 * @param Core_Models_Session $session
	 * @return int
	 */
	public function insert($session);
	
	/**
	 * Updates session data
	 * 
	 * @param Core_Models_Session $session
	 * @return int
	 */
	public function update($session);
}
