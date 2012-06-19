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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-05-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_FeedController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Shows the latest articles in RSS/Atom format
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$request	= $this->getRequest();
		$categoryId = $request->getParam('category_id');
		$userName   = $request->getParam('user_name');
		$type		= $request->getParam('type', Content_Models_Article::TYPE_ARTICLE);
		$format		= $request->getParam('feed_format', 'rss');
		$content	= Content_Services_Feed::getArticleFeeds($type, $format, $categoryId, $userName);
		
		header('Content-Type: application/rss+xml; charset=utf-8');
		$this->getResponse()->clearHeader('Content-Type')
							->setHeader('Content-Type', 'application/rss+xml; charset=utf-8');
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->getResponse()->setBody($content);
	}
}
