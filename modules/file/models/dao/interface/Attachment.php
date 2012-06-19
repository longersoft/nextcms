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
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface File_Models_Dao_Interface_Attachment
{
	/**
	 * Adds new attachment
	 * 
	 * @param File_Models_Attachment $attachment Attachment instance
	 * @return string Id of newly added attachment
	 */
	public function add($attachment);
	
	/**
	 * Gets the number of attachments by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return void
	 */
	public function delete($attachment);

	/**
	 * Finds attachments by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets attachment instance by given Id
	 * 
	 * @param string $attachmentId Id of attachment
	 * @return File_Models_Attachment|null
	 */
	public function getById($attachmentId);
	
	/**
	 * Increases the number of downloads of attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return void
	 */
	public function increaseNumDownloads($attachment);
	
	/**
	 * Updates given attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return void
	 */
	public function update($attachment);
	
	/**
	 * Updates the last download time of a given attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return void
	 */
	public function updateLastDownload($attachment);
}
