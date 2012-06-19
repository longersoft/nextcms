<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	models
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Tag_Models_Dao_Interface_Tag
{
	/**
	 * Adds new tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return string The Id of last inserted tag
	 */
	public function add($tag);
	
	/**
	 * Gets the number of tags by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public function delete($tag);
	
	/**
	 * Finds tags by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets tag instance by given Id
	 * 
	 * @param string $tagId The tag's Id
	 * @return Tag_Models_Tag
	 */
	public function getById($tagId);
	
	/**
	 * Builds a tag cloud
	 * 
	 * @param string $entityClass The entity's class
	 * @param string $language The tag's language
	 * @param int $count The number of tags
	 * @return Core_Base_Models_RecordSet
	 */
	public function getTagCloud($entityClass, $language = null, $count = 20);
	
	/**
	 * Updates given tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public function update($tag);
}
