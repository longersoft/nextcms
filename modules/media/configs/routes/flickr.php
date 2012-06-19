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
 * @version		2011-11-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for importing photos from Flickr
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	'media_flickr_auth' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/flickr/auth',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'flickr',
			'action'	 => 'auth',
			'allowed'    => 'media_flickr_import',
		),
	),	

	// Import photos
	'media_flickr_import' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/flickr/import',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'flickr',
			'action'	 => 'import',
		),
	),
	
	// List Flickr photos
	'media_flickr_photo' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/flickr/photo',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'flickr',
			'action'	 => 'photo',
			'allowed'    => 'media_flickr_import',
		),
	),
	
	// List Flickr sets
	'media_flickr_set' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/flickr/set',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'flickr',
			'action'	 => 'set',
			'allowed'    => 'media_flickr_import',
		),
	),
);
