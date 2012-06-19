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
 * @version		2012-06-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Widgets_Playlists_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$this->view->assign(array(
			'language'	=> $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
			'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
		));
	}
	
	/**
	 * Shows the playlists
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request    = $this->getRequest();
		$criteria   = array(
			'status'   => Media_Models_Playlist::STATUS_ACTIVATED,
			'title'    => $request->getParam('keyword', null),
			'language' => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
		);
		
		$count      = $request->getParam('limit', 20);
		$dataSource = $request->getParam('data_source');
		$photos     = array();
		
		switch ($dataSource) {
			// Get the playlists by their Ids
			case 'set':
				$criteria = array(
					'playlist_ids' => $request->getParam('playlist_ids', array()),
					'status'	   => Media_Models_Playlist::STATUS_ACTIVATED,
				);
				break;
			
			// Get the most viewed playlists
			case 'most_viewed':
				$criteria['sort_by']  = 'num_views';
				$criteria['sort_dir'] = 'DESC';
				break;
				
			// Get the latest activated playlists
			case 'latest':
			default:
				// Sort by the activated date
				$criteria['sort_by']  = 'activated_date';
				$criteria['sort_dir'] = 'DESC';
				break;
		}
		$playlists = Media_Services_Playlist::find($criteria, 0, $count);
		
		$this->view->assign(array(
			'title'		   => $request->getParam('title', ''),
			'playlists'	   => $playlists,
			'numPlaylists' => $playlists ? count($playlists) : 0,
		));
	}
}
