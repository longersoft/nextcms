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

class Comment_View_Helper_CommentAvatar extends Zend_View_Helper_Abstract
{
	/**
	 * Renders the img tag that shows avatar of commenter
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @param int $size Image size
	 * @return string
	 */
	public function commentAvatar($comment, $size = 80)
	{
		return $this->view->gravatar($comment->email, array('img_size' => $size));
	}
}
