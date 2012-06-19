<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-05-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_FeedController extends Zend_Controller_Action
{
	/**
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		header('Content-Type: application/rss+xml; charset=utf-8');
		$this->getResponse()->clearHeader('Content-Type')
							->setHeader('Content-Type', 'application/rss+xml; charset=utf-8');
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
	}
	
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Shows the latest videos in RSS/Atom format
	 * 
	 * @return void
	 */
	public function videoAction()
	{
		$request	= $this->getRequest();
		$playlistId = $request->getParam('playlist_id');
		$format		= $request->getParam('feed_format', 'rss');
		$content	= Media_Services_Feed::getVideoFeeds($format, $playlistId);
		$this->getResponse()->setBody($content);
	}
}
