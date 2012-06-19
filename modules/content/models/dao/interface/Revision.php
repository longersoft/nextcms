<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Content_Models_Dao_Interface_Revision
{
	/**
	 * Adds new revision
	 * 
	 * @param Content_Models_Revision $revision The revision instance
	 * @return string Id of newly created revision
	 */
	public function add($revision);
	
	/**
	 * Gets the number of revisions by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given revision
	 * 
	 * @param Content_Models_Revision $revision The revision instance
	 * @return void
	 */
	public function delete($revision);
	
	/**
	 * Finds revisions by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets revision instance by given Id
	 * 
	 * @param string $revisionId Id of the revision
	 * @return Content_Models_Revision|null
	 */
	public function getById($revisionId);
	
	/**
	 * Restores given revision
	 * 
	 * @param Content_Models_Revision $revision The revision instance
	 * @return void
	 */
	public function restore($revision);
}
