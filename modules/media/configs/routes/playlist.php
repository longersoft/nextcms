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
 * @version		2012-03-15
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing playlists
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// View playlist
	'media_playlist_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/playlist/view/(\w+)',
		'reverse'  => 'media/playlist/view/%s/',
		'map'	   => array(
			'1' => 'playlist_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'playlist.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'playlist.view.title',
				'params'		 => array(
					'playlist_id' => array(
						'name'	   => 'playlist_id',
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
				'default'	 => 'media/playlist/view/{playlist_id}',
				'predefined' => array(
					'media/playlist/{playlist_id}-{slug}.html',
				),
			),
		),
	),
	
	'media_playlist_view_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/playlist/view/(\w+)/page-(\d+)',
		'reverse'  => 'media/playlist/view/%s/page-%s',
		'map'	   => array(
			'1' => 'playlist_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'playlist.view.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'playlist.view.pager',
				'params'		 => array(
					'playlist_id' => array(
						'name'	   => 'playlist_id',
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
				'default'	 => 'media/playlist/view/{playlist_id}/page-{page}',
				'predefined' => array(
					'media/playlist/view/{slug}/page-{page}',
					'media/playlist/view/{playlist_id}-{slug}/page-{page}',
				),
			),
		),
	),

	////////// BACKEND ACTIONS //////////
	// Activate/deactivate playlist
	'media_playlist_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/playlist/activate',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'activate',
		),
	),

	// Add new playlist
	'media_playlist_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/playlist/add',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'add',
		),
	),
	
	// Update playlist's poster
	'media_playlist_cover' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/playlist/cover',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'cover',
		),
	),
	
	// Delete playlist
	'media_playlist_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/playlist/delete',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'delete',
		),
	),
	
	// List playlists
	'media_playlist_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/playlist/list',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'list',
		),
	),
	
	// Rename playlist
	'media_playlist_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/playlist/rename',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'playlist',
			'action'	 => 'rename',
		),
	),
);
