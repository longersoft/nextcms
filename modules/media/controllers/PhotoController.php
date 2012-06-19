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

class Media_PhotoController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Searches for photos
	 * 
	 * @return void
	 */
	public function searchAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$keyword  = $request->getParam('q');
		$page	  = $request->getParam('page', 1);
		
		// Filter the keyword
		$keyword  = strip_tags($keyword);
		$keyword  = Core_Base_Hook_Registry::getInstance()->executeFilter('Media_FilterSearchingKeyword', $keyword);
		$criteria = array(
			'status' => Media_Models_Photo::STATUS_ACTIVATED,
			'title'  => $keyword,
		);
		
		$perPage = 15;
		$photos  = Media_Services_Photo::find($criteria, ($page - 1) * $perPage, $perPage);
		$total   = Media_Services_Photo::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($photos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array(), 'media_photo_search') . '?page=__PAGE__&q=' . $keyword;
		
		// Highlight the search keyword in the title and description if they match the keyword
		Core_Base_Hook_Registry::getInstance()->register('Media_FilterPhotoTitle', array(Core_Filters_Highlight::getInstance(), 'filter'))
											  ->register('Media_FilterPhotoDescription', array(Core_Filters_Highlight::getInstance(), 'filter'));
		
		$this->view->assign(array(
			'photos'	=> $photos,
			'numPhotos' => $total,
			'keyword'	=> $criteria['title'],
			'paginator' => $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
	}
	
	/**
	 * Views photos by given tags
	 * 
	 * @return void
	 */
	public function tagAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$tagId    = $request->getParam('tag_id');
		$slug	  = $request->getParam('slug');
		$language = $request->getParam('lang');
		$page	  = $request->getParam('page', 1);
		
		if ($tagId) {
			$tag = Tag_Services_Tag::getById($tagId);
		} elseif ($slug) {
			$result = Tag_Services_Tag::find(array(
				'slug'	   => $slug,
				'language' => $language,
			), 0, 1);
			$tag	= ($request && count($result) > 0) ? $result[0] : null;
		}
		if ($tag == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the tag');
		}
		
		$perPage   = 15;
		$criteria  = array(
			'status' => Media_Models_Photo::STATUS_ACTIVATED,
			'tag'	 => $tag,
		);
		$photos    = Media_Services_Photo::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	   = Media_Services_Photo::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($photos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $tag->getProperties()), 'media_photo_tag_pager');
		
		$this->view->assign(array(
			'tag'		=> $tag,
			'photos'	=> $photos,
			'numPhotos'	=> $total,
			'paginator'	=> $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
	}
	
	/**
	 * Views photo details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$photoId = $request->getParam('photo_id');
		$slug	 = $request->getParam('slug');
		
		if ($photoId) {
			$photo = Media_Services_Photo::getById($photoId);
		} elseif ($slug) {
			$result = Media_Services_Photo::find(array(
				'slug'	 => $slug,
				'status' => Media_Models_Photo::STATUS_ACTIVATED,
			), 0, 1);
			$photo = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($photo == null || $photo->status != Media_Models_Photo::STATUS_ACTIVATED) {
			throw new Core_Base_Exception_NotFound('Cannot find the photo');
		}
		$request->setParam('user_id', $photo->user_id)
				->setParam('entity_class', get_class($photo))
				->setParam('entity_id', $photo->photo_id);
		
		// Set the meta tags
		if ($photo->description) {
			$this->view->headMeta()->setName('description', strip_tags($photo->description));
		}
		
		// Highlight the search keyword in the title and description if they match the keyword
		$referer   = $this->getRequest()->getServer('HTTP_REFERER');
		$searchUrl = $this->view->serverUrl() . $this->view->url(array(), 'media_photo_search');
		if ($referer && substr($referer, 0, strlen($searchUrl)) == $searchUrl) {
			Core_Base_Hook_Registry::getInstance()->register('Media_FilterPhotoTitle', array(Core_Filters_Highlight::getInstance(), 'filter'))
												  ->register('Media_FilterPhotoDescription', array(Core_Filters_Highlight::getInstance(), 'filter'));
		}
		
		// Filter the photo's description 
		$photo->description = Core_Base_Hook_Registry::getInstance()->executeFilter('Media_FilterPhotoDescription', $photo->description);
		
		$this->view->assign(array(
			'photo' => $photo,
			'tags'	=> Media_Services_Photo::getTags($photo),
		));
		
		// Update the number of views
		Core_Services_Counter::register($photo, 'views', 'Media_Services_Photo::increaseNumViews', array($photo));
	}
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Activates or deactivates photo
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$photoId = $request->getPost('photo_id');
		$photo   = Media_Services_Photo::getById($photoId);
		if (!$photo) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$photo->status = $photo->status == Media_Models_Photo::STATUS_ACTIVATED
							? Media_Models_Photo::STATUS_NOT_ACTIVATED
							: Media_Models_Photo::STATUS_ACTIVATED;
			$result = Media_Services_Photo::updateStatus($photo);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}

	/**
	 * Copies photo to album
	 * 
	 * @return void
	 */
	public function copyAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$albumId = $request->getPost('album_id');
		$photoId = $request->getPost('photo_id');
		$index	 = $request->getPost('index', 0);
		$result  = Media_Services_Photo::addToAlbum($photoId, $albumId, $index);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Deletes photo
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$photoId = $request->getParam('photo_id');
		$photo	 = Media_Services_Photo::getById($photoId);
		
		switch ($format) {
			case 'json':
				$result = Media_Services_Photo::delete($photo);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('photo', $photo);
				break;
		}
	}
	
	/**
	 * Downloads photo
	 * 
	 * @return void
	 */
	public function downloadAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('photo.download.title'));
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$photoId = $request->getParam('photo_id');
		$size	 = $request->getParam('size');
		$photo	 = Media_Services_Photo::getById($photoId);
		
		// Increase the number of downloads
		Core_Services_Counter::register($photo, 'downloads', 'Media_Services_Photo::increaseNumDownloads', array($photo));
		
		$file	 = APP_ROOT_DIR . $photo->__get('image_' . $size);
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
		}
		exit();
	}
	
	/**
	 * Edits the image
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$action   = $request->getParam('act');
		$size	  = $request->getParam('size');
		$path	  = $request->getParam('path');
		$original = $request->getParam('original');
		$photoId  = $request->getParam('photo_id', null);
		
		$editor = new Media_Services_PhotoEditor();
		$editor->setUser(Zend_Auth::getInstance()->getIdentity())
			   ->setThumbnailSize($size)
			   ->setSourceFile($path)
			   ->setOriginalFile($original);
		
		switch ($action) {
			case 'rotate':
				$angle = $request->getPost('angle');
				$url   = $editor->rotate($angle);
				$this->_helper->json(array(
					'path' => $url,
					'url'  => $this->view->APP_ROOT_URL . $url,
				));
				break;
				
			case 'flip':
				$direction = $request->getPost('direction');
				$url	   = $editor->flip($direction);
				$this->_helper->json(array(
					'path' => $url,
					'url'  => $this->view->APP_ROOT_URL . $url,
				));
				break;
			
			case 'crop':
				$width  = $request->getPost('w');
				$height = $request->getPost('h');
				$top	= $request->getPost('t');
				$left   = $request->getPost('l');
				$url	= $editor->crop($width, $height, $top, $left);
				$this->_helper->json(array(
					'path' => $url,
					'url'  => $this->view->APP_ROOT_URL . $url,
				));
				break;
				
			case 'resize':
				$width  = $request->getPost('w');
				$height = $request->getPost('h');
				$url	= $editor->resize($width, $height);
				$this->_helper->json(array(
					'path' => $url,
					'url'  => $this->view->APP_ROOT_URL . $url,
				));
				break;
				
			case 'save':
				$url = $editor->complete();
				$this->_helper->json(array(
					'photo_id' => $photoId,
					'size'	   => $size,
					'path'	   => $url,
					'url'	   => $this->view->APP_ROOT_URL . $url,
				));
				break;
				
			case 'clean':
				$editor->clean();
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'photo' => Media_Services_Photo::getById($photoId),
					'size'  => $size,
				));
				break;
		}
	}
	
	/**
	 * Lists photos
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$format   = $request->getParam('format');
		$q		  = $request->getParam('q');
		$default  = array(
			'page'		=> 1,
			'album_id'  => null,
			'title'		=> null,
			'status'	=> null,
			'per_page'	=> 20,
			'view_size'	=> 'thumbnail',
			'language'  => $language,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$photos	  = Media_Services_Photo::find($criteria, $offset, $criteria['per_page']);
		$total	  = Media_Services_Photo::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($photos, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'photos'	=> $photos,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Saves order of photos
	 * 
	 * @return void
	 */
	public function orderAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('photo.order.title'));
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$data	 = $request->getPost('data');
		$data	 = Zend_Json::decode($data);
		foreach ($data as $index => $item) {
			Media_Services_Photo::addToAlbum($item['photo_id'], $item['album_id'], $item['index']);
		}
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Removes photo from album
	 * 
	 * @return void
	 */
	public function removeAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('photo.remove.title'));
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$albumId = $request->getParam('album_id');
		$photoId = $request->getParam('photo_id');
		$result  = Media_Services_Photo::removeFromAlbum($photoId, $albumId);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Renames photo
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$photo	 = new Media_Models_Photo(array(
							'photo_id' => $request->getPost('photo_id'),
							'title'	   => $request->getPost('title'),
						));
		$result  = Media_Services_Photo::rename($photo);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Replaces photo
	 * 
	 * @return void
	 */
	public function replaceAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$photoId = $request->getParam('photo_id');
		$photo   = Media_Services_Photo::getById($photoId);
		
		switch ($format) {
			case 'json':
				$currThumbs = $photo->getThumbnails();
				$newThumbs  = File_Services_Uploader::upload('uploadedfiles', 'media', array('thumbnail' => true, 'watermark' => true));
				$result		= array();
				foreach ($newThumbs as $thumb) {
					foreach ($thumb as $size => $data) {
						// Overwrite original thumbnail with the new one
						$currPath = APP_ROOT_DIR . rtrim(DS . str_replace('/', DS, ltrim($currThumbs[$size], DS)), DS);
						$newPath  = APP_ROOT_DIR . rtrim(DS . str_replace('/', DS, ltrim($data['url'], DS)), DS);
						
						if (file_exists($currPath) && file_exists($newPath)) {
							@copy($newPath, $currPath);
							@unlink($newPath);
						}
						
						$result[] = array(
							'photo_id' => $photoId,
							'path'	   => $currThumbs[$size],
							'size'	   => $size,
						);
					}
				}
				
				// Returns the array in JSON format that will be processed by 
				// handler of the onComplete() event of Dojo Uploader widget
				$this->_helper->json($result);
				break;
			default:
				$this->view->assign('photo', $photo);
				break;
		}
	}
	
	/**
	 * Updates photo information
	 * 
	 * @return void
	 */
	public function updateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$photoId = $request->getParam('photo_id');
		$photo   = Media_Services_Photo::getById($photoId);
		
		switch ($format) {
			case 'json':
				$photo->title		 = $request->getPost('title');
				$photo->description  = $request->getPost('description');
				$photo->photographer = $request->getPost('photographer');
				
				$result = Media_Services_Photo::update($photo);
				
				// Set tags
				if ($tagIds = $request->getPost('tags')) {
					$tags = array();
					foreach ($tagIds as $tagId) {
						$tags[] = Tag_Services_Tag::getById($tagId);
					}
					Media_Services_Photo::setTags($photo, $tags);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'photo' => $photo,
					'tags'  => Media_Services_Photo::getTags($photo),
				));
				break;
		}
	}
	
	/**
	 * Uploads photos
	 * 
	 * @return void
	 */
	public function uploadAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$format   = $request->getParam('format');
		$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		
		switch ($format) {
			case 'json':
				$albumId = $request->getPost('album_id');
				$files   = $request->getPost('photos');
				
				foreach ($files as $index => $file) {
					$file  = Zend_Json::decode($file);
					// FIXME: Generate slug based on the title
					$photo = new Media_Models_Photo(array(
						'title'			  => $file['name'],
						'image_square'	  => $file['square'],
						'image_thumbnail' => $file['thumbnail'],
						'image_small'	  => $file['small'],
						'image_crop'	  => $file['crop'],
						'image_medium'	  => $file['medium'],
						'image_large'	  => $file['large'],
						'image_original'  => $file['original'],
						'uploaded_date'	  => date('Y-m-d H:i:s'),
						'user_id'		  => Zend_Auth::getInstance()->getIdentity()->user_id,
						'user_name'		  => Zend_Auth::getInstance()->getIdentity()->user_name,
						'language'		  => $language,
					));
					$photoId = Media_Services_Photo::add($photo);
					Media_Services_Photo::addToAlbum($photoId, $albumId, $index);
				}
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
				
			default:
				$albumId = $request->getParam('album_id');
				$this->view->assign(array(
					'album'    => Media_Services_Album::getById($albumId),
					'albums'   => Media_Services_Album::find(array('language' => $language)),
					'language' => $language,
				));
				break;
		}
	}	
}
