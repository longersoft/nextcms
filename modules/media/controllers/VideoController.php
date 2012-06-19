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

class Media_VideoController extends Zend_Controller_Action
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
	 * Embeds a video player
	 * 
	 * @return void
	 */
	public function embedAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
		Core_Services_Db::connect('slave');
		
		$request = $this->getRequest();
		$videoId = $request->getParam('video_id');
		$video   = $videoId ? Media_Services_Video::getById($videoId) : null;
		if ($video == null || $video->status != Media_Models_Video::STATUS_ACTIVATED) {
			throw new Core_Base_Exception_NotFound('Cannot find the video');
		}
		$this->view->assign('video', $video);
	}
	
	/**
	 * Lists the videos in different conditions
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$sortBy   = $request->getParam('sort_by', 'latest');
		$page	  = $request->getParam('page', 1);
		$perPage  = 15;
		$criteria = array(
			'status'   => Media_Models_Video::STATUS_ACTIVATED,
			'sort_by'  => null,
			'sort_dir' => 'DESC',
		);
		
		switch ($sortBy) {
			case 'most-commented':
				$criteria['sort_by'] = 'num_comments';
				break;
			case 'most-viewed':
				$criteria['sort_by'] = 'num_views';
				break;
			case 'lastest':
			default:
				$criteria['sort_by'] = 'activated_date';
				break;
		}
		
		$videos  = Media_Services_Video::find($criteria, ($page - 1) * $perPage, $perPage);
		$total   = Media_Services_Video::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($videos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array('page' => '__PAGE__', 'sort_by' => $sortBy), 'media_video_index_pager');
		
		// Show RSS link in the head section
		$this->view->headLink(array(
			'rel' 	=> 'alternate', 
			'type' 	=> 'application/rss+xml', 
			'href' 	=> $this->view->serverUrl() . $this->view->url(array('feed_format' => 'atom'), 'media_feed_video'),
		));
		
		$this->view->assign(array(
			'videos'	=> $videos,
			'numVideos'	=> $total,
			'paginator'	=> $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
	}
	
	/**
	 * Searches for videos
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
			'status' => Media_Models_Video::STATUS_ACTIVATED,
			'title'  => $keyword,
		);
		
		$perPage = 15;
		$videos  = Media_Services_Video::find($criteria, ($page - 1) * $perPage, $perPage);
		$total   = Media_Services_Video::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($videos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array(), 'media_video_search') . '?page=__PAGE__&q=' . $keyword;
		
		// Highlight the search keyword in the title and description if they match the keyword
		Core_Base_Hook_Registry::getInstance()->register('Media_FilterVideoTitle', array(Core_Filters_Highlight::getInstance(), 'filter'))
											  ->register('Media_FilterVideoDescription', array(Core_Filters_Highlight::getInstance(), 'filter'));
		
		$this->view->assign(array(
			'videos'	=> $videos,
			'numVideos' => $total,
			'keyword'	=> $criteria['title'],
			'paginator' => $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
	}
	
	/**
	 * Views videos by given tags
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
		
		$perPage   = 20;
		$criteria  = array(
			'status' => Media_Models_Video::STATUS_ACTIVATED,
			'tag'	 => $tag,
		);
		$videos    = Media_Services_Video::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	   = Media_Services_Video::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($videos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $tag->getProperties()), 'media_video_tag_pager');
		
		$this->view->assign(array(
			'tag'		=> $tag,
			'videos'	=> $videos,
			'numVideos'	=> $total,
			'paginator'	=> $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
	}
	
	/**
	 * Views video details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$videoId = $request->getParam('video_id');
		$slug	 = $request->getParam('slug');
		
		if ($videoId) {
			$video = Media_Services_Video::getById($videoId);
		} elseif ($slug) {
			$result = Media_Services_Video::find(array(
				'slug'	 => $slug,
				'status' => Media_Models_Video::STATUS_ACTIVATED,
			), 0, 1);
			$video = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($video == null || $video->status != Media_Models_Video::STATUS_ACTIVATED) {
			throw new Core_Base_Exception_NotFound('Cannot find the video');
		}
		$request->setParam('user_id', $video->user_id)
				->setParam('entity_class', get_class($video))
				->setParam('entity_id', $video->video_id);
		
		// Set the meta tags
		if ($video->description) {
			$this->view->headMeta()->setName('description', strip_tags($video->description));
		}
		
		// Highlight the search keyword in the title and description if they match the keyword
		$referer   = $this->getRequest()->getServer('HTTP_REFERER');
		$searchUrl = $this->view->serverUrl() . $this->view->url(array(), 'media_video_search');
		if ($referer && substr($referer, 0, strlen($searchUrl)) == $searchUrl) {
			Core_Base_Hook_Registry::getInstance()->register('Media_FilterVideoTitle', array(Core_Filters_Highlight::getInstance(), 'filter'))
												  ->register('Media_FilterVideoDescription', array(Core_Filters_Highlight::getInstance(), 'filter'));
		}
		
		// Filter the video's description 
		$video->description = Core_Base_Hook_Registry::getInstance()->executeFilter('Media_FilterVideoDescription', $video->description);
		
		$this->view->assign(array(
			'video' => $video,
			'tags'	=> Media_Services_Video::getTags($video),
		));
		
		// Update the number of views
		Core_Services_Counter::register($video, 'views', 'Media_Services_Video::increaseNumViews', array($video));
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates or deactivates video
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$videoId = $request->getPost('video_id');
		$video   = Media_Services_Video::getById($videoId);
		
		if (!$video) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$video->status = $video->status == Media_Models_Video::STATUS_ACTIVATED
							? Media_Models_Video::STATUS_NOT_ACTIVATED
							: Media_Models_Video::STATUS_ACTIVATED;
			$result = Media_Services_Video::updateStatus($video);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}
	
	/**
	 * Adds new video
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$language   = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$format		= $request->getParam('format');
		$playlistId = $request->getParam('playlist_id');
		
		switch ($format) {
			case 'json':
				$video = new Media_Models_Video(array(
					'slug'			=> $request->getPost('slug'),
					'title'			=> $request->getPost('title'),
					'description'	=> $request->getPost('description'),
					'uploaded_date'	=> date('Y-m-d H:i:s'),
					'user_id'		=> Zend_Auth::getInstance()->getIdentity()->user_id,
					'user_name'		=> Zend_Auth::getInstance()->getIdentity()->user_name,
					'url'			=> $request->getPost('url'),	
					'embed_code'	=> $request->getPost('embed_code'),
					'duration'		=> $request->getPost('duration', '00:00:00'),
					'credit'		=> $request->getPost('credit'),
					'language'		=> $language,
				));
				$poster = $request->getPost('poster');
				if ($poster) {
					$poster = Zend_Json::decode($poster);
					foreach ($poster as $key => $value) {
						$video->__set('image_' . $key, $value);
					}
				}
				$videoId = Media_Services_Video::add($video);
				if ($playlistId) {
					Media_Services_Video::addToPlaylist($videoId, $playlistId);
				}
				
				// Set tags
				$video->video_id = $videoId;
				if ($tagIds = $request->getPost('tags')) {
					$tags = array();
					foreach ($tagIds as $tagId) {
						$tags[] = Tag_Services_Tag::getById($tagId);
					}
					Media_Services_Video::setTags($video, $tags);
				}
				
				$this->_helper->json(array(
					'result'	  => 'APP_RESULT_OK',
					'playlist_id' => $playlistId,
				));
				break;
			default:
				$playlist = $playlistId ? Media_Services_Playlist::getById($playlistId) : null;
				
				$this->view->assign(array(
					'playlist'  => $playlist,
					'playlists' => Media_Services_Playlist::find(array('language' => $language)),
					'language'  => $language,
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Copies video to playlist
	 * 
	 * @return void
	 */
	public function copyAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$playlistId = $request->getPost('playlist_id');
		$videoId	= $request->getPost('video_id');
		$index		= $request->getPost('index', 0);
		$result		= Media_Services_Video::addToPlaylist($videoId, $playlistId, $index);
		$this->_helper->json(array(
			'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'video_id'	  => $videoId,
			'playlist_id' => $playlistId,
		));
	}
	
	/**
	 * Sets video's poster
	 * 
	 * @return void
	 */
	public function coverAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('video.cover.title'));
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$videoId	= $request->getParam('video_id');
		$thumbnails = $request->getParam('thumbnails');
		$thumbnails = Zend_Json::decode($thumbnails);
		
		$video = Media_Services_Video::getById($videoId);
		foreach ($thumbnails as $key => $value) {
			$video->__set('image_' . $key, $value);
		}
		
		$result = Media_Services_Video::update($video);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Deletes video
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$videoId = $request->getParam('video_id');
		$video	 = Media_Services_Video::getById($videoId);
		
		switch ($format) {
			case 'json':
				$result = Media_Services_Video::delete($video);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'video'	  => $video,
					'videoId' => $videoId,
				));
				break;
		}
	}
	
	/**
	 * Downloads video
	 * 
	 * @return void
	 */
	public function downloadAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$videoId = $request->getParam('video_id');
		$video	 = Media_Services_Video::getById($videoId);
		
		$file	 = APP_ROOT_DIR . $video->url;
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
	 * Lists videos
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
			'page'		  => 1,
			'playlist_id' => null,
			'title'		  => null,
			'status'	  => null,
			'per_page'	  => 20,
			'view_size'	  => 'thumbnail',
			'language'	  => $language,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();		
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$videos	  = Media_Services_Video::find($criteria, $offset, $criteria['per_page']);
		$total	  = Media_Services_Video::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($videos, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);

		$this->view->assign(array(
			'videos'	=> $videos,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Saves order of videos
	 * 
	 * @return void
	 */
	public function orderAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('video.order.title'));
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$data	 = $request->getPost('data');
		$data	 = Zend_Json::decode($data);
		foreach ($data as $index => $item) {
			Media_Services_Video::addToPlaylist($item['video_id'], $item['playlist_id'], $item['index']);
		}
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Removes video from playlist
	 * 
	 * @return void
	 */
	public function removeAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('video.remove.title'));
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$playlistId = $request->getPost('playlist_id');
		$videoId	= $request->getPost('video_id');
		$result		= Media_Services_Video::removeFromPlaylist($videoId, $playlistId);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Renames video
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$videoId = $request->getParam('video_id');
		$video	 = Media_Services_Video::getById($videoId);
		
		switch ($format) {
			case 'json':
				$result = true;
				if ($video) {
					$video->title = $request->getPost('title');
					$video->slug  = $request->getPost('slug');
					$result = Media_Services_Video::rename($video);
				}
				$this->_helper->json(array(
							'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
							'video_id'	  => $videoId,
							'title'		  => $video ? $video->title : null, 
							'short_title' => $video ? $this->view->stringFormatter()->sub($video->title, 20) : null,
						));
				break;
			default:
				$this->view->assign(array(
					'video'	  => $video,
					'videoId' => $videoId,
				));
				break;
		}
	}
	
	/**
	 * Updates video
	 * 
	 * @return void
	 */
	public function updateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$videoId = $request->getParam('video_id');
		$video   = Media_Services_Video::getById($videoId);
		
		switch ($format) {
			case 'json':
				$video->slug		= $request->getPost('slug');
				$video->title		= $request->getPost('title');
				$video->description = $request->getPost('description');
				$video->url			= $request->getPost('url');
				$video->embed_code	= $request->getPost('embed_code');
				$video->duration	= $request->getPost('duration', '00:00:00');
				$video->credit		= $request->getPost('credit');
				$video->language	= $request->getPost('language');
				
				$poster = $request->getPost('poster');
				if ($poster) {
					$poster = Zend_Json::decode($poster);
					foreach ($poster as $key => $value) {
						$video->__set('image_' . $key, $value);
					}
				}
				
				$result = Media_Services_Video::update($video);
				
				// Set tags
				if ($tagIds = $request->getPost('tags')) {
					$tags = array();
					foreach ($tagIds as $tagId) {
						$tags[] = Tag_Services_Tag::getById($tagId);
					}
					Media_Services_Video::setTags($video, $tags);
				}
				
				$this->_helper->json(array(
					'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'video_id'	  => $videoId,
					'title'		  => $video ? $video->title : null,
					'short_title' => $video ? $this->view->stringFormatter()->sub($video->title, 20) : null,
					'url'		  => $video ? $video->url : null,
					'embed_code'  => $video ? $video->embed_code : null,
					'thumbnails'  => $video ? $video->getPosterThumbnails() : null,
				));
				break;
			default:
				$this->view->assign(array(
					'video'	    => $video,
					'videoId'   => $videoId,
					'tags'	    => Media_Services_Video::getTags($video),
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
}
