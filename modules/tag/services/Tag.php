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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_Services_Tag
{
	/**
	 * Adds new tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return string The Id of last inserted tag
	 */
	public static function add($tag)
	{
		if (!$tag || !($tag instanceof Tag_Models_Tag)) {
			throw new Exception('The param is not an instance of Tag_Models_Tag');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'tag',
									'name'   => 'Tag',
								))
								->setDbConnection($conn)
								->add($tag);
	}
	
	/**
	 * Gets the number of tags by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'tag',
									'name'   => 'Tag',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return bool
	 */
	public static function delete($tag)
	{
		if (!$tag || !($tag instanceof Tag_Models_Tag)) {
			throw new Exception('The param is not an instance of Tag_Models_Tag');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'tag',
							'name'   => 'Tag',
						 ))
						 ->setDbConnection($conn)
						 ->delete($tag);
		// Execute hooks
		Core_Base_Hook_Registry::getInstance()->executeAction('Tag_Services_Tag_DeleteTag', array($tag));
	
		return true;
	}
	
	/**
	 * Finds tags by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'tag',
									'name'   => 'Tag',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets tag instance by given Id
	 * 
	 * @param string $tagId The tag's Id
	 * @return Tag_Models_Tag
	 */
	public static function getById($tagId)
	{
		if ($tagId == null || empty($tagId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'tag',
									'name'   => 'Tag',
								))
								->setDbConnection($conn)
								->getById($tagId);
	}
	
	/**
	 * Builds a tag cloud
	 * 
	 * @param string $routeName The name of route which lists tags
	 * @param string $entityClass The entity's class
	 * @param string $language The tag's language
	 * @param int $count The number of tags
	 * @return Zend_Tag_Cloud
	 */
	public static function getTagCloud($routeName, $entityClass, $language = null, $count = 20)
	{
		$conn = Core_Services_Db::getConnection();
		$tags = Core_Services_Dao::factory(array(
									'module' => 'tag',
									'name'   => 'Tag',
								 ))
								 ->setDbConnection($conn)
								 ->getTagCloud($entityClass, $language, $count);
		$items = array();
		$view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		if ($tags == null) {
			return null;
		}
		
		$route = Zend_Controller_Front::getInstance()->getRouter();
		foreach ($tags as $tag) {
			$items[] = array(
				'title'  => $tag->title,
				'weight' => $tag->weight,
				'params' => array(
					'url' => $route->hasRoute($routeName) ? $view->url($tag->getProperties(), $routeName) : '',
				),
			);
		}
		
		return new Zend_Tag_Cloud(array('tags' => $items));
	}
	
	/**
	 * Updates given tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return bool
	 */
	public static function update($tag)
	{
		if (!$tag || !($tag instanceof Tag_Models_Tag)) {
			throw new Exception('The param is not an instance of Tag_Models_Tag');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'tag',
							'name'   => 'Tag',
						 ))
						 ->setDbConnection($conn)
						 ->update($tag);
		// Execute hooks
		Core_Base_Hook_Registry::getInstance()->executeAction('Tag_Services_Tag_DeleteTag', array($tag));
		
		return true;
	}
}
