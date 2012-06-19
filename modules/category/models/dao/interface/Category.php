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
 * @subpackage	models
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Category_Models_Dao_Interface_Category
{
	/**
	 * Adds new category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return string Id of newly created category
	 */
	public function add($category);
	
	/**
	 * Deletes category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return void
	 */
	public function delete($category);

	/**
	 * Gets category by given id
	 * 
	 * @param string $categoryId Id of category
	 * @return Category_Models_Category
	 */
	public function getById($categoryId);
	
	/**
	 * Gets category by given slug
	 * 
	 * @param string $slug The slug
	 * @param string $module The module's name
	 * @param string $language The language
	 * @return Category_Models_Category
	 */
	public function getBySlug($slug, $module, $language);
	
	/**
	 * Gets list of parent categories of given category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return Core_Base_Models_RecordSet
	 */
	public function getParents($category);
	
	/**
	 * Gets category tree
	 * 
	 * @param string $module The name of module
	 * @param string $language The language
	 * @param Category_Models_Category $root The root category
	 * @return Core_Base_Models_RecordSet
	 */
	public function getTree($module, $language, $root = null);
	
	/**
	 * Moves category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return void
	 */
	public function move($category);
	
	/**
	 * Renames category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return void
	 */
	public function rename($category);
	
	/**
	 * Updates category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @return void
	 */
	public function update($category);
}
