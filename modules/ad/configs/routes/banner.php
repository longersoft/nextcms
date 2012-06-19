<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing banners
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Activate/deactivate banner
	'ad_banner_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/banner/activate',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'banner',
			'action'	 => 'activate',
		),
	),

	// Add new banner
	'ad_banner_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/banner/add',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'banner',
			'action'	 => 'add',
		),
	),
	
	// Delete banner
	'ad_banner_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/banner/delete',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'banner',
			'action'	 => 'delete',
		),
	),
	
	// Edit banner
	'ad_banner_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/banner/edit',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'banner',
			'action'	 => 'edit',
		),
	),
	
	// List banners
	'ad_banner_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/banner/list',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'banner',
			'action'	 => 'list',
		),
	),
	
	// Place banner on pages
	'ad_banner_place' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/banner/place',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'banner',
			'action'	 => 'place',
		),
	),
);
