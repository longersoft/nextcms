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
 * @version		2011-11-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_FlickrController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Imports photos
	 * 
	 * @return void
	 */
	public function importAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$photos = $request->getParam('photos');
				$photos = Zend_Json::decode($photos);
				$result = true;
				
				foreach ($photos as $key => $value) {
					$result = $result && Media_Services_Photo::importFromFlickr(array(
						'flickr_id'  => $key,
						'title'		 => $value['title'],
						'thumbnails' => $value['thumbnails'],
					));
				}

				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$authenticated = Media_Services_Flickr::getInstance()->getUserId() ? true : false;
				$this->view->assign('authenticated', $authenticated);
				break;
		}
	}
	
	/**
	 * Authenticates
	 * 
	 * @return void
	 */
	public function authAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$frob    = $request->getParam('frob');
		
		if (!$frob) {
			$this->_redirect(Media_Services_Flickr::getInstance()->getAuthUrl());
		} else {
			Media_Services_Flickr::getInstance()->authenticate($frob);
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index') . '#u=' . $this->view->url(array(), 'media_flickr_import'));
		}
	}
	
	/**
	 * Lists photos of given set
	 * 
	 * @return void
	 */
	public function photoAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$setId   = $request->getParam('set_id');
		$photos  = Media_Services_Flickr::getInstance()->getPhotos($setId);
		
		$this->view->assign('photos', $photos);
	}
	
	/**
	 * Lists Flickr sets
	 * 
	 * @return void
	 */
	public function setAction()
	{
		Core_Services_Db::connect('master');
		
		$userId = Media_Services_Flickr::getInstance()->getUserId();
		$sets   = Media_Services_Flickr::getInstance()->getSets($userId);
		
		$this->view->assign('sets', $sets);
	}
}
