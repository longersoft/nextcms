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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_View_Helper_ArticleCounter
{
	/**
	 * Gets the number of articles by given conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function articleCounter($criteria = array())
	{
		Core_Services_Db::connect('master');
		return Content_Services_Article::count($criteria);
	}
}
