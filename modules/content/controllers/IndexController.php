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
 * @version		2012-05-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_IndexController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Shows homepage of the Content module
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		// Show RSS link in the head section
		$this->view->headLink(array(
			'rel' 	=> 'alternate', 
			'type' 	=> 'application/rss+xml', 
			'href' 	=> $this->view->serverUrl() . $this->view->url(array('feed_format' => 'atom', 'type' => Content_Models_Article::TYPE_ARTICLE), 'content_feed_index'),
		));
	}
}
