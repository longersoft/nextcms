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
 * @version		2012-02-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_Page
{
	/**
	 * Adds new page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return string Id of newly created page
	 */
	public function add($page);
	
	/**
	 * Gets the number of pages by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return void
	 */
	public function delete($page);
	
	/**
	 * Deletes all pages belong to given template
	 * 
	 * @param string $template The template name
	 * @return void
	 */
	public function deleteByTemplate($template);
	
	/**
	 * Finds pages by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets page instance by given Id
	 * 
	 * @param string $pageId Page's Id
	 * @return Core_Models_Error|null
	 */
	public function getById($pageId);
	
	/**
	 * Updates given page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return void
	 */
	public function update($page);
	
	/**
	 * Updates the layout of page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return void
	 */
	public function updateLayout($page);
}
