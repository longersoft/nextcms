<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	services
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_Services_Category
{
	/**
	 * Adds new category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return string Id of newly created category
	 */
	public static function add($category)
	{
		if (!$category || !($category instanceof Category_Models_Category)) {
			throw new Exception('The input param is not an instance of Category_Models_Category');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Category',
								))
								->setDbConnection($conn)
								->add($category);
	}
	
	/**
	 * Deletes category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return bool
	 */
	public static function delete($category)
	{
		if (!$category || !($category instanceof Category_Models_Category)) {
			throw new Exception('The param is not an instance of Category_Models_Category');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
								'module' => 'category',
								'name'	 => 'Category',
						 ))
						 ->setDbConnection($conn)
						 ->delete($category);
		
		// Execute hook
		Core_Base_Hook_Registry::getInstance()->executeAction('Category_CategoryDeleted_' . $category->module);
		
		return true;
	}
	
	/**
	 * Gets category by given id
	 * 
	 * @param string $categoryId Id of category
	 * @return Category_Models_Category
	 */
	public static function getById($categoryId)
	{
		if (!$categoryId) {
			throw new Exception('The Id of category is required');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Category',
								))
								->setDbConnection($conn)
								->getById($categoryId);
	}
	
	/**
	 * Gets category by given slug
	 * 
	 * @param string $slug The slug
	 * @param string $module The module's name
	 * @param string $language The language
	 * @return Category_Models_Category
	 */
	public static function getBySlug($slug, $module, $language = null)
	{
		if (!$slug || !$module) {
			throw new Exception('The slug and module are required');
		}
		if ($language == null) {
			$language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Category',
								))
								->setDbConnection($conn)
								->getBySlug($slug, $module, $language);
	}
	
	/**
	 * Gets list of parent categories of given category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getParents($category)
	{
		if (!$category || !($category instanceof Category_Models_Category)) {
			throw new Exception('The param is not an instance of Category_Models_Category');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Category',
								))
								->setDbConnection($conn)
								->getParents($category);
	}
	
	/**
	 * Gets category tree
	 * 
	 * @param string $module The name of module
	 * @param string $language The language
	 * @param Category_Models_Category $root The root category
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getTree($module, $language, $root = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'category',
									'name'	 => 'Category',
								))
								->setDbConnection($conn)
								->getTree($module, $language, $root);
	}
	
	/**
	 * Gets the data to build a Dojo tree
	 * 
	 * @param string $module The name of module
	 * @param string $language The language
	 * @return array
	 */
	public static function getTreeData($module, $language)
	{
		// Map category's Id with its children
		$children	= array();
		$categories = self::getTree($module, $language);
		foreach ($categories as $category) {
			$parentId = $category->parent_id;
			if ($parentId == null) {
				continue;
			}
			$parentId = (string) $parentId;
			if (!isset($children[$parentId])) {
				$children[$parentId] = array();
			}
			$children[$parentId][] = $category;
		}
		
		$data = array(
			'identifier' => 'category_id',
			'label'		 => 'name',
			'items'		 => self::_getTreeData('0', $children),
		);
		return $data;
	}
	
	/**
	 * @param string $parentId Id of parent category
	 * @param array $children Its children
	 * @return array
	 */
	private static function _getTreeData($parentId, $children)
	{
		$parentId = (string) $parentId;
		if (!isset($children[$parentId])) {
			return array();
		}
		$items = array();
		foreach ($children[$parentId] as $category) {
			$item = array(
				'category_id'	=> $category->category_id,
				'name'			=> $category->name,
				'language'		=> $category->language,
				'translations'  => $category->translations,
				'numberOfItems' => isset($children[$category->category_id . '']) ? count($children[$category->category_id . '']) : 0,
			);
			if ($item['numberOfItems'] > 0) {
				$item['children'] = self::_getTreeData($category->category_id, $children);
			}
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Moves category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return bool
	 */
	public static function move($category)
	{
		if (!$category || !($category instanceof Category_Models_Category)) {
			throw new Exception('The param is not an instance of Category_Models_Category');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
								'module' => 'category',
								'name'	 => 'Category',
						 ))
						 ->setDbConnection($conn)
						 ->move($category);
		return true;
	}
	
	/**
	 * Renames category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return bool
	 */
	public static function rename($category)
	{
		if (!$category || !($category instanceof Category_Models_Category)) {
			throw new Exception('The param is not an instance of Category_Models_Category');
		}
		if (!$category->name) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
								'module' => 'category',
								'name'	 => 'Category',
						 ))
						 ->setDbConnection($conn)
						 ->rename($category);
		return true;
	}
	
	/**
	 * Updates category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return bool
	 */
	public static function update($category)
	{
		if (!$category || !($category instanceof Category_Models_Category)) {
			throw new Exception('The param is not an instance of Category_Models_Category');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
								'module' => 'category',
								'name'	 => 'Category',
						 ))
						 ->setDbConnection($conn)
						 ->update($category);
		return true;
	}
}
