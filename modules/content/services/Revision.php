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
 * @subpackage	services
 * @since		1.0
 * @version		2012-01-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Services_Revision
{
	/**
	 * Adds new revision
	 * 
	 * @param Content_Models_Revision $revision The revision instance
	 * @return string Id of newly created revision
	 */
	public static function add($revision)
	{
		if (!$revision || !($revision instanceof Content_Models_Revision)) {
			throw new Exception('The first param is not an instance of Content_Models_Revision');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Revision',
								))
								->setDbConnection($conn)
								->add($revision);
	}
	
	/**
	 * Gets the number of revisions by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Revision',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given revision
	 * 
	 * @param Content_Models_Revision $revision The revision instance
	 * @return bool
	 */
	public static function delete($revision)
	{
		if (!$revision || !($revision instanceof Content_Models_Revision)) {
			throw new Exception('The first param is not an instance of Content_Models_Revision');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
								'module' => 'content',
								'name'   => 'Revision',
						 ))
						 ->setDbConnection($conn)
						 ->delete($revision);
		return true;
	}
	
	/**
	 * Finds revisions by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Revision',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets revision instance by given Id
	 * 
	 * @param string $revisionId Id of the revision
	 * @return Content_Models_Revision|null
	 */
	public static function getById($revisionId)
	{
		if (!$revisionId) {
			throw new Exception("The revision's Id is required");
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Revision',
								))
								->setDbConnection($conn)
								->getById($revisionId);
	}
	
	/**
	 * Restores given revision
	 * 
	 * @param Content_Models_Revision $revision The revision instance
	 * @return bool
	 */
	public static function restore($revision)
	{
		if (!$revision || !($revision instanceof Content_Models_Revision)) {
			throw new Exception('The first param is not an instance of Content_Models_Revision');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
								'module' => 'content',
								'name'   => 'Revision',
						 ))
						 ->setDbConnection($conn)
						 ->restore($revision);
						 
		// Restore tags
		if ($revision->tags) {
			$tags = array();
			foreach (explode(',', $revision->tags) as $tagId) {
				if ($tag = Tag_Services_Tag::getById($tagId)) {
					$tags[] = $tag;
				}
			}
			$article = new Content_Models_Article($revision->getProperties());
			Content_Services_Article::setTags($article, $tags);
		}
		
		return true;
	}
}
