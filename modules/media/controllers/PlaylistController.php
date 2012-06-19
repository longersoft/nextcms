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

class Media_PlaylistController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Views playlist details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$playlistId = $request->getParam('playlist_id');
		$slug	 	= $request->getParam('slug');
		$page	 	= $request->getParam('page', 1);
		
		if ($playlistId) {
			$playlist = Media_Services_Playlist::getById($playlistId);
		} elseif ($slug) {
			$result = Media_Services_Playlist::find(array(
				'slug'	 => $slug,
				'status' => Media_Models_Playlist::STATUS_ACTIVATED,
			), 0, 1);
			$playlist = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($playlist == null || $playlist->status != Media_Models_Playlist::STATUS_ACTIVATED) {
			throw new Core_Base_Exception_NotFound('Cannot find the playlist');
		}
		$request->setParam('user_id', $playlist->user_id)
				->setParam('entity_class', get_class($playlist))
				->setParam('entity_id', $playlist->playlist_id);
		
		// Set the meta tags
		if ($playlist->description) {
			$this->view->headMeta()->setName('description', strip_tags($playlist->description));
		}
		
		// Filter the album's description 
		$playlist->description = Core_Base_Hook_Registry::getInstance()->executeFilter('Media_FilterPlaylistDescription', $playlist->description);
		
		// Get playlist's videos
		$criteria = array(
			'playlist_id' => $playlist->playlist_id,
			'status'   => Media_Models_Playlist::STATUS_ACTIVATED,
		);
		$perPage  = 10;
		$videos	  = Media_Services_Video::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	  = Media_Services_Video::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($videos, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $playlist->getProperties()), 'media_playlist_view_pager');
		
		$this->view->assign(array(
			'playlist'	=> $playlist,
			'videos'	=> $videos,
			'paginator'	=> $this->view->paginator('sliding')->render($paginator, $pagerPath),
		));
		
		// Update the number of views
		Core_Services_Counter::register($playlist, 'views', 'Media_Services_Playlist::increaseNumViews', array($playlist));
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates or deactivates playlist
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$playlistId = $request->getPost('playlist_id');
		$playlist   = Media_Services_Playlist::getById($playlistId);
		
		if (!$playlist) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$playlist->status = $playlist->status == Media_Models_Playlist::STATUS_ACTIVATED
							? Media_Models_Playlist::STATUS_NOT_ACTIVATED
							: Media_Models_Playlist::STATUS_ACTIVATED;
			$result = Media_Services_Playlist::updateStatus($playlist);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}
	
	/**
	 * Adds new playlist
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
				$playlist = new Media_Models_Playlist(array(
					'title'		   => $request->getPost('title'),
					'slug'		   => $request->getPost('slug'),
					'created_date' => date('Y-m-d H:i:s'),
					'user_id'	   => Zend_Auth::getInstance()->getIdentity()->user_id,
					'user_name'	   => Zend_Auth::getInstance()->getIdentity()->user_name,
					'language'	   => $request->getPost('language'),
				));
				$playlistId = Media_Services_Playlist::add($playlist);
				
				$this->_helper->json(array(
					'result'	  => 'APP_RESULT_OK',
					'playlist_id' => $playlistId,
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
	 * Sets poster for playlist
	 * 
	 * @return void
	 */
	public function coverAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$playlistId = $request->getPost('playlist_id');
		$videoId	= $request->getPost('video_id');
		$thumbs		= $request->getParam('thumbnails');
		
		$playlist	= Media_Services_Playlist::getById($playlistId);
		$video		= null;
		$thumbnails = array(
			'video_id' => $videoId,
		);
		
		if ($videoId) {
			$video		= Media_Services_Video::getById($videoId);
			$thumbnails = $video->getPosterThumbnails();
		} elseif ($thumbs) {
			$thumbnails = Zend_Json::decode($thumbs);
		}
		
		$result = Media_Services_Playlist::updatePoster($playlist, $thumbnails, $video);
		$this->_helper->json(array(
			'result'	 => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'thumbnails' => $thumbnails,
		));
	}
	
	/**
	 * Deletes playlist
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$format		= $request->getParam('format');
		$playlistId = $request->getParam('playlist_id');
		$playlist	= Media_Services_Playlist::getById($playlistId);
		
		switch ($format) {
			case 'json':
				$result = Media_Services_Playlist::delete($playlist);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('playlist', $playlist);
				break;
		}
	}
	
	/**
	 * Lists playlists
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
			'page'				 => 1,
			'status'			 => null,
			'active_playlist_id' => null,
			'view_type'			 => 'list',
			'per_page'			 => 20,
			'language'			 => $language,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();		
		$criteria  = array_merge($default, $criteria);
		$offset	   = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$playlists = Media_Services_Playlist::find($criteria, $offset, $criteria['per_page']);
		$total	   = Media_Services_Playlist::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($playlists, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'playlists' => $playlists,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Renames playlist
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$format		= $request->getParam('format');
		$playlistId = $request->getParam('playlist_id');
		$playlist	= Media_Services_Playlist::getById($playlistId);
		
		switch ($format) {
			case 'json':
				$result = true;
				if ($playlist) {
					$playlist->title = $request->getPost('title');
					$playlist->slug	 = $request->getPost('slug');
					$result = Media_Services_Playlist::rename($playlist);
				}
				$this->_helper->json(array(
							'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
							'playlist_id' => $playlistId,
							'title'		  => $playlist ? $playlist->title : null, 
							'short_title' => $playlist ? $this->view->stringFormatter()->sub($playlist->title, 20) : null,
						));
				break;
			default:
				$this->view->assign(array(
					'playlist'	 => $playlist,
					'playlistId' => $playlistId,
				));
				break;
		}
	}
}
