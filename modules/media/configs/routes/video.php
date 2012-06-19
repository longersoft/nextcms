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
 * @version		2012-05-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing videos
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Embed a video player
	'media_video_embed' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/embed/(\w+)',
		'reverse'  => 'media/video/embed/%s/',
		'map'	   => array(
			'1' => 'video_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'embed',
		),
	),
	
	// List videos in different criterias 
	'media_video_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/index/([\w-]+)',
		'reverse'  => 'media/video/index/%s/',
		'map'	   => array(
			'1' => 'sort_by',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'index',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.index.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.index.title',
				'params'		 => array(
					'sort_by' => array(
						'name'	   => 'sort_by',
						'regex'	   => '([\w-]+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/video/index/{sort_by}',
				'predefined' => array(),
			),
		),
	),
	
	// List videos in different criterias (pager_
	'media_video_index_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/index/([\w-]+)/page-(\d+)',
		'reverse'  => 'media/video/index/%s/page-%s',
		'map'	   => array(
			'1' => 'sort_by',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'index',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.index.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.index.pager',
				'params'		 => array(
					'sort_by' => array(
						'name'	   => 'sort_by',
						'regex'	   => '([\w-]+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'page' => array(
						'name'	   => 'page',
						'regex'	   => '(\d+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/video/index/{sort_by}/page-{page}',
				'predefined' => array(),
			),
		),
	),
	
	// Search for videos
	'media_video_search' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'media/video/search',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'search',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.search.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.search.title',
				'params'		 => array(),
				'type'		 	 => 'default',
				'default'	 	 => 'media/video/search',
			),
		),
	),
	
	// View videos by given tag
	'media_video_tag' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/tag/(\w+)',
		'reverse'  => 'media/video/tag/%s/',
		'map'	   => array(
			'1' => 'tag_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.tag.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.tag.title',
				'params'		 => array(
					'tag_id' => array(
						'name'	   => 'tag_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'slug' => array(
						'name'	   => 'slug',
						'regex'	   => '([\w-_]+)',
						'reverse'  => '%s',
						'required' => false,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/video/tag/{tag_id}',
				'predefined' => array(
					'media/video/tag/{slug}',
					'media/video/tag/{tag_id}-{slug}'
				),
			),
		),
	),
	
	// View videos by given tag (pager)
	'media_video_tag_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/tag/(\w+)/page-(\d+)',
		'reverse'  => 'media/video/tag/%s/page-%s',
		'map'	   => array(
			'1' => 'tag_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.tag.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.tag.pager',
				'params'		 => array(
					'tag_id' => array(
						'name'	   => 'tag_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'slug' => array(
						'name'	   => 'slug',
						'regex'	   => '([\w-_]+)',
						'reverse'  => '%s',
						'required' => false,
					),
					'page' => array(
						'name'	   => 'page',
						'regex'	   => '(\d+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/video/tag/{tag_id}/page-{page}',
				'predefined' => array(
					'media/video/tag/{slug}/page-{page}',
					'media/video/tag/{tag_id}-{slug}/page-{page}'
				),
			),
		),
	),

	// View video
	'media_video_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/view/(\w+)',
		'reverse'  => 'media/video/view/%s/',
		'map'	   => array(
			'1' => 'video_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.view.title',
				'params'		 => array(
					'video_id' => array(
						'name'	   => 'video_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'slug' => array(
						'name'	   => 'slug',
						'regex'	   => '([\w-_]+)',
						'reverse'  => '%s',
						'required' => false,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/video/view/{video_id}',
				'predefined' => array(
					'media/video/{video_id}-{slug}.html',
				),
			),
		),
	),
	
	// View video in playlist
	'media_video_playlist_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/video/playlist/(\w+)/(\w+)',
		'reverse'  => 'media/video/playlist/%s/%s/',
		'map'	   => array(
			'1' => 'video_id',
			'2' => 'playlist_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'video.view.inPlaylistTitle',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'video.view.inPlaylistTitle',
				'params'		 => array(
					'video_id' => array(
						'name'	   => 'video_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'slug' => array(
						'name'	   => 'slug',
						'regex'	   => '([\w-_]+)',
						'reverse'  => '%s',
						'required' => false,
					),
					'playlist_id' => array(
						'name'	   => 'playlist_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/video/playlist/{video_id}/{playlist_id}',
				'predefined' => array(
					'media/video/playlist/{video_id}-{playlist_id}/{slug}.html',
				),
			),
		),
	),

	////////// BACKEND ACTIONS //////////
	// Activate/deactivate video
	'media_video_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/activate',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'activate',
		),
	),

	// Add new video
	'media_video_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/add',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'add',
		),
	),
	
	// Copy video to playlist
	'media_video_copy' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/copy',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'copy',
		),
	),
	
	// Update video's poster
	'media_video_cover' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/cover',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'cover',
		),
	),
	
	// Delete video
	'media_video_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/delete',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'delete',
		),
	),
	
	// Download video
	'media_video_download' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/download',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'download',
		),
	),
	
	// List videos
	'media_video_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/list',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'list',
		),
	),
	
	// Save order of videos in playlist
	'media_video_order' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/order',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'order',
		),
	),
	
	// Remove video from playlist
	'media_video_remove' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/remove',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'remove',
		),
	),
	
	// Rename video
	'media_video_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/rename',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'rename',
		),
	),
	
	// Update basic information
	'media_video_update' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/video/update',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'video',
			'action'	 => 'update',
		),
	),
);
