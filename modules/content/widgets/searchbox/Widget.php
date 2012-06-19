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

class Content_Widgets_Searchbox_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the search box
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		$type	 = $request->getParam('type', Content_Models_Article::TYPE_ARTICLE);
		$this->view->assign(array(
			'route' => (Content_Models_Article::TYPE_BLOG == $type) ? 'content_blog_search' : 'content_article_search',
			'title' => $request->getParam('title', ''),
		));
	}
}
