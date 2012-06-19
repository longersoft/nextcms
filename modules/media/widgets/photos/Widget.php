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

class Media_Widgets_Photos_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows albums in given language
	 * 
	 * @return void
	 */
	public function albumAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$store    = array(
			'identifier' => 'album_id',
			'label'		 => 'title',
			'items'		 => array(
				array(
					'album_id' => '',
					'title'    => $this->view->translator()->_('config.selectAlbum'),
				),
				array(
					'album_id' => '__AUTO__',
					'title'		  => $this->view->translator()->_('config.albumDeterminedAutomatically'),
				),
			),
		);
		$albums	  = Media_Services_Album::find(array(
			'status'   => Media_Models_Album::STATUS_ACTIVATED,
			'sort_by'  => 'title',
			'sort_dir' => 'ASC',
			'language' => $language,
		));
		if ($albums) {
			foreach ($albums as $album) {
				$store['items'][] = array(
					'album_id' => $album->album_id,
					'title'	   => $album->title,
				);
			}
		}
		echo Zend_Json::encode($store);
	}
	
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
		$photoIds   = $request->getParam('photo_ids');
		$dataSource = $request->getParam('data_source');
		
		$photos     = array();
		if ($dataSource == 'set' && $photoIds) {
			$resultSet = Media_Services_Photo::find(array(
				'photo_ids' => $photoIds,
			));
			foreach ($resultSet as $photo) {
				$photos[] = $photo->getProperties(array('photo_id', 'title', 'image_square'));
			}
		}
		
		// Build data store for albums filtering select
		$store = array(
			'identifier' => 'album_id',
			'label'		 => 'title',
			'items'		 => array(
				array(
					'album_id' => '',
					'title'    => $this->view->translator()->_('config.selectAlbum'),
				),
				array(
					'album_id' => '__AUTO__',
					'title'		  => $this->view->translator()->_('config.albumDeterminedAutomatically'),
				),
			),
		);
		$albums = Media_Services_Album::find(array(
			'status'   => Media_Models_Album::STATUS_ACTIVATED,
			'sort_by'  => 'title',
			'sort_dir' => 'ASC',
			'language' => $language,
		));
		if ($albums) {
			foreach ($albums as $album) {
				$store['items'][] = array(
					'album_id' => $album->album_id,
					'title'	   => $album->title,
				);
			}
		}
		
		$this->view->assign(array(
			'language'	=> $language,
			'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
			'uid'	    => uniqid(),
			'albums'    => Zend_Json::encode($store),
			'photos'    => $photos,
			'album_id'  => $request->getParam('album_id', ''),
		));
	}
	
	/**
	 * Shows the photos
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request    = $this->getRequest();
		$criteria   = array(
			'status'   => Media_Models_Photo::STATUS_ACTIVATED,
			'title'    => $request->getParam('keyword', null),
			'album_id' => $request->getParam('album_id', null),
			'language' => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
		);
		$count      = $request->getParam('limit', 20);
		$dataSource = $request->getParam('data_source');
		$photos     = array();
		
		switch ($dataSource) {
			// Get the photos by their Ids
			case 'set':
				$photoIds = $request->getParam('photo_ids', array());
				$criteria = array(
					'photo_ids' => $photoIds,
					'status'	=> Media_Models_Photo::STATUS_ACTIVATED,
				);
				break;
			
			// Get the most viewed photos
			case 'most_viewed':
				$criteria['sort_by']  = 'num_views';
				$criteria['sort_dir'] = 'DESC';
				break;
				
			// Most commented photos
			case 'most_commented':
				$criteria['sort_by']  = 'num_comments';
				$criteria['sort_dir'] = 'DESC';
				break;
			
			// Most downloaded photos
			case 'most_downloaded':
				$criteria['sort_by']  = 'num_downloads';
				$criteria['sort_dir'] = 'DESC';
				break;
				
			// Get the latest activated photos
			case 'latest':
			default:
				// Sort by the activated date
				$criteria['sort_by']  = 'activated_date';
				$criteria['sort_dir'] = 'DESC';
				break;
		}
		$photos = Media_Services_Photo::find($criteria, 0, $count);
		
		$this->view->assign(array(
			'title'		=> $request->getParam('title', ''),
			'photos'	=> $photos,
			'album'		=> $criteria['album_id'] ? Media_Services_Album::getById($criteria['album_id']) : null,
			'numPhotos' => $photos ? count($photos) : 0,
		));
	}
}
