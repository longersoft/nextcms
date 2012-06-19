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
 * Define routes for downloading actions
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Download a photo
	'media_download_photo' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'media/download/photo/(\w+)',
		'reverse'  => 'media/download/photo/%s/',
		'map'	   => array(
			'1' => 'photo_id',
		),
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'download',
			'action'	 => 'photo',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'download.photo.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'download.photo.title',
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
				'default'	 => 'media/download/photo/{photo_id}',
				'predefined' => array(
					'media/download/photo/{photo_id}-{slug}.html',
				),
			),
		),
	),
);
