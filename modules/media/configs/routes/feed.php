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
 * @subpackage	configs
 * @since		1.0
 * @version		2012-05-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for showing RSS/Atom feeds
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// View the latest activated videos of given playlist in RSS/Atom format
	'media_feed_playlist' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/feed/video/(\w+)/(\w+).xml',
		'reverse'  => 'media/feed/video/%s/%s.xml',
		'map'	   => array(
			'1' => 'feed_format',
			'2' => 'playlist_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'feed',
			'action'	 => 'video',
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'feed.video.playlist',
				'params'		 => array(
					'feed_format' => array(
						'name'	   => 'feed_format',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'playlist_id' => array(
						'name'	   => 'playlist_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/feed/video/{feed_format}/{playlist_id}.xml',
				'predefined' => array(),
			),
		),
	),
	
	// View the latest activated videos in RSS/Atom format
	'media_feed_video' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/feed/video/(\w+).xml',
		'reverse'  => 'media/feed/video/%s.xml',
		'map'	   => array(
			'1' => 'feed_format',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'feed',
			'action'	 => 'video',
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'feed.video.title',
				'params'		 => array(
					'feed_format' => array(
						'name'	   => 'feed_format',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'	  => 'default',
				'default' => 'media/feed/video/{feed_format}.xml',
			),
		),
	),
);
