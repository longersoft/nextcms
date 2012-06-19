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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Widgets_Flickr_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the photos
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request  = $this->getRequest();
		$apiKey   = $request->getParam('api_key');
		$limit	  = $request->getParam('limit', 10);
		$searchBy = $request->getParam('search_by');
		
		$flickr = new Zend_Service_Flickr($apiKey);
		switch ($searchBy) {
			case 'tag':
				$tag = $request->getParam('tag');
				$set = $flickr->tagSearch($tag, array('per_page' => $limit));
				break;
			case 'user':
				$user = $request->getParam('user');
				$set  = $flickr->userSearch($user, array('per_page' => $limit));
				break;
			default:
				$set = null;
				break;
		}
		$this->view->assign(array(
			'set'  => $set,
			'size' => $request->getParam('size', 'Medium'),
		));
	}
}
