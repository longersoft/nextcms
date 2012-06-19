<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_View_Helper_CommentCounter
{
	/**
	 * Gets the number of comments by given conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function commentCounter($criteria = array())
	{
		Core_Services_Db::connect('master');
		return Comment_Services_Comment::count($criteria);
	}
}
