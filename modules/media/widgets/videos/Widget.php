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
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Widgets_Videos_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request    = $this->getRequest();
		$language   = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$videoIds   = $request->getParam('video_ids');
		$dataSource = $request->getParam('data_source');
		
		$videos     = array();
		if ($dataSource == 'set' && $videoIds) {
			$resultSet = Media_Services_Video::find(array(
				'video_ids' => $videoIds,
			));
			foreach ($resultSet as $video) {
				$videos[] = $video->getProperties(array('video_id', 'title', 'image_square'));
			}
		}
		
		// Build data store for playlist filtering select
		$store = array(
			'identifier' => 'playlist_id',
			'label'		 => 'title',
			'items'		 => array(
				array(
					'playlist_id' => '',
					'title'		  => $this->view->translator()->_('config.selectPlaylist'),
				),
				array(
					'playlist_id' => '__AUTO__',
					'title'		  => $this->view->translator()->_('config.playlistDeterminedAutomatically'),
				),
			),
		);
		$playlists = Media_Services_Playlist::find(array(
			'status'   => Media_Models_Playlist::STATUS_ACTIVATED,
			'sort_by'  => 'title',
			'sort_dir' => 'ASC',
			'language' => $language,
		));
		if ($playlists) {
			foreach ($playlists as $playlist) {
				$store['items'][] = array(
					'playlist_id' => $playlist->playlist_id,
					'title'		  => $playlist->title,
				);
			}
		}
		
		$this->view->assign(array(
			'language'	  => $language,
			'languages'   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
			'uid'		  => uniqid(),
			'playlists'   => Zend_Json::encode($store),
			'videos'	  => $videos,
			'playlist_id' => $request->getParam('playlist_id', ''),
		));
	}
	
	/**
	 * Shows playlists in given language
	 * 
	 * @return void
	 */
	public function playlistAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$store    = array(
			'identifier' => 'playlist_id',
			'label'		 => 'title',
			'items'		 => array(
				array(
					'playlist_id' => '',
					'title'    	  => $this->view->translator()->_('config.selectPlaylist'),
				),
				array(
					'playlist_id' => '__AUTO__',
					'title'		  => $this->view->translator()->_('config.playlistDeterminedAutomatically'),
				),
			),
		);
		$playlists = Media_Services_Playlist::find(array(
			'status'   => Media_Models_Playlist::STATUS_ACTIVATED,
			'sort_by'  => 'title',
			'sort_dir' => 'ASC',
			'language' => $language,
		));
		if ($playlists) {
			foreach ($playlists as $playlist) {
				$store['items'][] = array(
					'playlist_id' => $playlist->playlist_id,
					'title'		  => $playlist->title,
				);
			}
		}
		echo Zend_Json::encode($store);
	}
	
	/**
	 * Shows the videos
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request    = $this->getRequest();
		$criteria   = array(
			'status'	  => Media_Models_Video::STATUS_ACTIVATED,
			'title'		  => $request->getParam('keyword', null),
			'playlist_id' => $request->getParam('playlist_id', null),
			'language'	  => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
		);
		$count      = $request->getParam('limit', 20);
		$dataSource = $request->getParam('data_source');
		$videos     = array();
		
		$link       = null;
		switch ($dataSource) {
			// Get the videos by their Ids
			case 'set':
				$videoIds = $request->getParam('video_ids', array());
				$criteria = array(
					'video_ids' => $videoIds,
					'status'	=> Media_Models_Video::STATUS_ACTIVATED,
				);
				break;
			
			// Get the most viewed videos
			case 'most_viewed':
				$criteria['sort_by']  = 'num_views';
				$criteria['sort_dir'] = 'DESC';
				$link				  = $this->view->url(array('sort_by' => 'most-viewed'), 'media_video_index');
				break;
			
			// Get the most commented videos
			case 'most_commented':
				$criteria['sort_by']  = 'num_comments';
				$criteria['sort_dir'] = 'DESC';
				$link				  = $this->view->url(array('sort_by' => 'most-commented'), 'media_video_index');
				break;
				
			// Get the latest activated videos
			case 'latest':
			default:
				// Sort by the activated date
				$criteria['sort_by']  = 'activated_date';
				$criteria['sort_dir'] = 'DESC';
				$link				  = $this->view->url(array('sort_by' => 'latest'), 'media_video_index');
				break;
		}
		$videos = Media_Services_Video::find($criteria, 0, $count);
		
		$this->view->assign(array(
			'title'		=> $request->getParam('title', ''),
			'videos'	=> $videos,
			'numVideos' => $videos ? count($videos) : 0,
			'criteria'  => $criteria,
			'link'	    => $link,
		));
	}
}
