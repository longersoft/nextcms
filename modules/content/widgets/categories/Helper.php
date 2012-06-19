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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Widgets_Categories_Helper
{
	/**
	 * Gets this view helper instance
	 * 
	 * @return Content_Widgets_Categories_Helper
	 */
	public function helper()
	{
		return $this;
	}
	
	/**
	 * Gets the number of activated articles in given category
	 * 
	 * @param Category_Models_Category $category The category instance
	 * @param string $language The article's language
	 * @param string $type The type of article, which can be "article", "blog", "page"
	 * @return int
	 */
	public function getNumArticles($category, $language, $type = Content_Models_Article::TYPE_BLOG)
	{
		Core_Services_Db::connect('slave');
		
		return Content_Services_Article::count(array(
			'category_id' => $category->category_id,
			'type'		  => $type,
			'status'	  => Content_Models_Article::STATUS_ACTIVATED,
			'language'    => $language,
		));
	}
}
