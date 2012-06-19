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
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Tasks_Feedimporter_Helper
{
	/**
	 * @var mixed
	 */
	private $_conn = null;
	
	/**
	 * Gets the helper instance
	 * 
	 * @return Content_Tasks_Feedimporter_Helper
	 */
	public function helper()
	{
		return $this;
	}
	
	/**
	 * Gets categories tree
	 * 
	 * @param string $language The language
	 * @return Core_Base_Models_RecordSet
	 */
	public function getCategories($language)
	{
		if ($this->_conn == null) {
			$this->_conn = Core_Services_Db::connect('master');
		}
		return Category_Services_Category::getTree('content', $language);
	}
}
