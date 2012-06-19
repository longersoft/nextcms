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
 * @version		2012-06-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_AlbumController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Views album details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$albumId = $request->getParam('album_id');
		$slug	 = $request->getParam('slug');
		$page	 = $request->getParam('page', 1);
		
		if ($albumId) {
			$album = Media_Services_Album::getById($albumId);
		} elseif ($slug) {
			$result = Media_Services_Album::find(array(
				'slug'	 => $slug,
				'status' => Media_Models_Album::STATUS_ACTIVATED,
			), 0, 1);
			$album = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($album == null || $album->status != Media_Models_Album::STATUS_ACTIVATED) {
			throw new Core_Base_Exception_NotFound('Cannot find the album');
		}
		$request->setParam('user_id', $album->user_id)
				->setParam('entity_class', get_class($album))
				->setParam('entity_id', $album->album_id);
		
		// Set the meta tags
		if ($album->description) {
			$this->view->headMeta()->setName('description', strip_tags($album->description));
		}
		
		// Filter the album's description 
		$album->description = Core_Base_Hook_Registry::getInstance()->executeFilter('Media_FilterAlbumDescription', $album->description);
		
		// Get album's photos
		$criteria = array(
			'album_id' => $album->album_id,
			'status'   => Media_Models_Photo::STATUS_ACTIVATED,
		);
		$perPage  = 9;
		$photos	  = Media_Services_Photo::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	  = Media_Services_Photo::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($photos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $album->getProperties()), 'media_album_view_pager');
		
		$this->view->assign(array(
			'album'		=> $album,
			'photos'	=> $photos,
			'paginator'	=> $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
		
		// Update the number of views
		Core_Services_Counter::register($album, 'views', 'Media_Services_Album::increaseNumViews', array($album));
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates or deactivates album
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$albumId = $request->getPost('album_id');
		$album   = Media_Services_Album::getById($albumId);
		if (!$album) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$album->status = $album->status == Media_Models_Album::STATUS_ACTIVATED
							? Media_Models_Album::STATUS_NOT_ACTIVATED
							: Media_Models_Album::STATUS_ACTIVATED;
			$result = Media_Services_Album::updateStatus($album);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}
	
	/**
	 * Adds new album
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$album   = new Media_Models_Album(array(
					'title'		   => $request->getPost('title'),
					'slug'		   => $request->getPost('slug'),
					'created_date' => date('Y-m-d H:i:s'),
					'user_id'	   => Zend_Auth::getInstance()->getIdentity()->user_id,
					'user_name'	   => Zend_Auth::getInstance()->getIdentity()->user_name,
					'language'	   => $request->getPost('language'),
				));
				$albumId = Media_Services_Album::add($album);
				
				$this->_helper->json(array(
					'result'   => 'APP_RESULT_OK',
					'album_id' => $albumId,
				));
				break;
			default:
				$this->view->assign(array(
					'language'  => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Sets cover for album
	 * 
	 * @return void
	 */
	public function coverAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$albumId = $request->getPost('album_id');
		$photoId = $request->getPost('photo_id');
		$thumbs  = $request->getParam('thumbnails');
		
		$album		= Media_Services_Album::getById($albumId);
		$photo		= null;
		$thumbnails = array(
			'photo_id' => $photoId,
		);
		
		if ($photoId) {
			$photo		= Media_Services_Photo::getById($photoId);
			$thumbnails = $photo->getThumbnails();
		} elseif ($thumbs) {
			$thumbnails = Zend_Json::decode($thumbs);
		}
		
		$result = Media_Services_Album::updateCover($album, $thumbnails, $photo);
		$this->_helper->json(array(
			'result'	 => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'thumbnails' => $thumbnails,
		));
	}
	
	/**
	 * Deletes album
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$albumId = $request->getParam('album_id');
		$album	 = Media_Services_Album::getById($albumId);
		
		switch ($format) {
			case 'json':
				$result = Media_Services_Album::delete($album);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('album', $album);
				break;
		}
	}
	
	/**
	 * Lists albums
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$q		  = $request->getParam('q');
		$default  = array(
			'page'			  => 1,
			'status'		  => null,
			'active_album_id' => null,		// To show the selected album
			'view_type'		  => 'list',	// To show the list as a list or grid
			'per_page'		  => 20,
			'language'		  => $language,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$albums	  = Media_Services_Album::find($criteria, $offset, $criteria['per_page']);
		$total	  = Media_Services_Album::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($albums, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'albums'	=> $albums,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator
		));
	}
	
	/**
	 * Renames album
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$title	 = $request->getPost('title');
		$album	 = new Media_Models_Album(array(
							'album_id' => $request->getPost('album_id'),
							'title'	   => $title,
						));
		$result = Media_Services_Album::rename($album);
		$this->_helper->json(array(
							'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
							'title'		  => $title, 
							'short_title' => $this->view->stringFormatter()->sub($title, 20),
						));
	}
}
