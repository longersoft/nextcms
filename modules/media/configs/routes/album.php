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
 * Define routes for managing albums
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// View album
	'media_album_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/album/view/(\w+)',
		'reverse'  => 'media/album/view/%s/',
		'map'	   => array(
			'1' => 'album_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'album.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'album.view.title',
				'params'		 => array(
					'album_id' => array(
						'name'	   => 'album_id',
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
				'default'	 => 'media/album/view/{album_id}',
				'predefined' => array(
					'media/album/view/{album_id}-{slug}.html',
				),
			),
		),
	),
	
	'media_album_view_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/album/view/(\w+)/page-(\d+)',
		'reverse'  => 'media/album/view/%s/page-%s',
		'map'	   => array(
			'1' => 'album_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'album.view.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'album.view.pager',
				'params'		 => array(
					'album_id' => array(
						'name'	   => 'album_id',
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
				'default'	 => 'media/album/view/{album_id}/page-{page}',
				'predefined' => array(
					'media/album/view/{slug}/page-{page}',
					'media/album/view/{album_id}-{slug}/page-{page}',
				),
			),
		),
	),

	////////// BACKEND ACTIONS //////////
	// Activate/deactivate album
	'media_album_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/album/activate',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'activate',
		),
	),

	// Add new album
	'media_album_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/album/add',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'add',
		),
	),
	
	// Update album's cover
	'media_album_cover' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/album/cover',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'cover',
		),
	),
	
	// Delete album
	'media_album_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/album/delete',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'delete',
		),
	),
	
	// List albums
	'media_album_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/album/list',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'list',
		),
	),
	
	// Rename album
	'media_album_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/album/rename',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'album',
			'action'	 => 'rename',
		),
	),
);
