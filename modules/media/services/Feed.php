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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Services_Feed
{
	/**
	 * Gets the latest activated videos in RSS/Atom format
	 * 
	 * @param string $format Format of feed entries. Can be "rss" or "atom"
	 * @param string $playlistId Id of playlist
	 * @return string
	 */
	public static function getVideoFeeds($format = 'rss', $playlistId = null)
	{
		Core_Services_Db::connect('slave');
		
		// Build searching criteria
		$limit	  = Core_Services_Config::get('core', 'feed_limit', 20);
		$criteria = array(
			'status'	  => Media_Models_Video::STATUS_ACTIVATED,
			'playlist_id' => $playlistId,
		);
		$videos = Media_Services_Video::find($criteria, 0, $limit);
		
		// Create feed entries
		$view 	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$entries = array();
		if ($videos && count($videos) > 0) {
			foreach ($videos as $video) {
				$link 		 = $playlistId
							 ? $view->serverUrl() . $view->url(array_merge(array('playlist_id' => $playlistId), $video->getProperties()), 'media_video_playlist_view')
							 : $view->serverUrl() . $view->url($video->getProperties(), 'media_video_view');		
				$description = $video->description;
				$image 		 = $video->getPoster('thumbnail');
				$description = (null == $image || '' == $image) 
								? $description
								: '<a href="' . $link . '" title="' . addslashes($video->title) . '"><img src="' . $image . '" title="' . addslashes($video->title) . '" /></a>' . $description;
				$entries[] 	 = array(
									'title'		  => $video->title,
									'guid'		  => $link, 
									'link'		  => $link,
									'description' => $description,
									'content'	  => $description,
									'lastUpdate'  => strtotime($video->activated_date),
								);
			}
		}
		
		// Generate feed output
		$playlist  = $playlistId ? Media_Services_Playlist::getById($playlistId) : null;
		$link	   = $playlist
				   ? $view->serverUrl() . $view->url($playlist->getProperties(), 'media_playlist_view')
				   : Core_Services_Config::get('core', 'url_base', $view->serverUrl());
		$buildDate = strtotime(date('D, d M Y h:i:s'));
		$data 	   = array(
						'title'		  => $playlist ? $playlist->title : Core_Services_Config::get('core', 'feed_title', ''),
						'link'		  => $link,
						'description' => $playlist ? $playlist->title : Core_Services_Config::get('core', 'feed_description', ''),
						'copyright'   => Core_Services_Config::get('core', 'feed_copyright', ''),
						'generator'   => Core_Services_Config::get('core', 'feed_generator', Core_Services_Version::getVersion()),
						'lastUpdate'  => $buildDate,
						'published'   => $buildDate,
						'charset' 	  => 'UTF-8',
						'entries' 	  => $entries,
					);
		$feed 	   = Zend_Feed::importArray($data, $format);
		$xmlFeed   = $feed->saveXML();
		return $xmlFeed;
	}
}
