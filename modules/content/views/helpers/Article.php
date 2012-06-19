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
 * @subpackage	views
 * @since		1.0
 * @version		2012-05-03
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_View_Helper_Article
{
	/**
	 * Gets this view helper instance
	 * 
	 * @return Content_View_Helper_Article
	 */
	public function article()
	{
		return $this;
	}
	
	/**
	 * Gets the previous activated article in the same category with given article
	 * 
	 * @param Content_Models_Article $article The article
	 * @return Content_Models_Article
	 */
	public function getPrevArticle($article)
	{
		Core_Services_Db::connect('slave');
		return Content_Services_Article::getPrevArticle($article);
	}
	
	/**
	 * Gets the next activated article in the same category with given article
	 * 
	 * @param Content_Models_Article $article The article
	 * @return Content_Models_Article
	 */
	public function getNextArticle($article)
	{
		Core_Services_Db::connect('slave');
		return Content_Services_Article::getNextArticle($article);
	}
}
