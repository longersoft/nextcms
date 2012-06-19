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
 * Define routes for managing photos
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Search for photos
	'media_photo_search' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'media/photo/search',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'search',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'photo.search.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'photo.search.title',
				'params'		 => array(),
				'type'		 	 => 'default',
				'default'	 	 => 'media/photo/search',
			),
		),
	),
	
	// View photos by given tag
	'media_photo_tag' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/photo/tag/(\w+)',
		'reverse'  => 'media/photo/tag/%s/',
		'map'	   => array(
			'1' => 'tag_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'photo.tag.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'photo.tag.title',
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
				'default'	 => 'media/photo/tag/{tag_id}',
				'predefined' => array(
					'media/photo/tag/{slug}',
					'media/photo/tag/{tag_id}-{slug}'
				),
			),
		),
	),
	
	// View photos by given tag (pager)
	'media_photo_tag_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/photo/tag/(\w+)/page-(\d+)',
		'reverse'  => 'media/photo/tag/%s/page-%s',
		'map'	   => array(
			'1' => 'tag_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'photo.tag.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'photo.tag.pager',
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
				'default'	 => 'media/photo/tag/{tag_id}/page-{page}',
				'predefined' => array(
					'media/photo/tag/{slug}/page-{page}',
					'media/photo/tag/{tag_id}-{slug}/page-{page}'
				),
			),
		),
	),

	// View photo
	'media_photo_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/photo/view/(\w+)',
		'reverse'  => 'media/photo/view/%s/',
		'map'	   => array(
			'1' => 'photo_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'photo.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'photo.view.title',
				'params'		 => array(
					'photo_id' => array(
						'name'	   => 'photo_id',
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
				'default'	 => 'media/photo/view/{photo_id}',
				'predefined' => array(
					'media/photo/{photo_id}-{slug}.html',
				),
			),
		),
	),
	
	// View photo in album
	'media_photo_album_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/photo/album/(\w+)/(\w+)',
		'reverse'  => 'media/photo/album/%s/%s/',
		'map'	   => array(
			'1' => 'photo_id',
			'2' => 'album_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'photo.view.inAlbumTitle',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'photo.view.inAlbumTitle',
				'params'		 => array(
					'photo_id' => array(
						'name'	   => 'photo_id',
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
					'album_id' => array(
						'name'	   => 'album_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'media/photo/album/{photo_id}/{album_id}',
				'predefined' => array(
					'media/photo/album/{photo_id}-{album_id}/{slug}.html',
				),
			),
		),
	),

	////////// BACKEND ACTIONS //////////
	// Activate/deactivate photo
	'media_photo_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/activate',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'activate',
		),
	),

	// Copy photo to album
	'media_photo_copy' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/copy',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'copy',
		),
	),
	
	// Delete photo
	'media_photo_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/delete',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'delete',
		),
	),
	
	// Download photo
	'media_photo_download' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/download',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'download',
		),
	),
	
	// Edit photo using Photo Editor
	'media_photo_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/edit',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'edit',
		),
	),
	
	// List photos
	'media_photo_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/list',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'list',
		),
	),
	
	// Save order of photos in album
	'media_photo_order' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/order',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'order',
		),
	),
	
	// Remove photo from album
	'media_photo_remove' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/remove',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'remove',
		),
	),
	
	// Rename photo
	'media_photo_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/rename',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'rename',
		),
	),
	
	// Replace photo
	'media_photo_replace' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/replace',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'replace',
		),
	),
	
	// Update basic information
	'media_photo_update' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/update',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'update',
		),
	),
	
	// Upload photos
	'media_photo_upload' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/photo/upload',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'photo',
			'action'	 => 'upload',
		),
	),
);
