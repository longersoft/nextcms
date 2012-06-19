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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing zones
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new zone
	'ad_zone_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/zone/add',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'zone',
			'action'	 => 'add',
		),
	),
	
	// Delete zone
	'ad_zone_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/zone/delete',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'zone',
			'action'	 => 'delete',
		),
	),
	
	// Edit zone
	'ad_zone_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/zone/edit',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'zone',
			'action'	 => 'edit',
		),
	),
	
	// List zones
	'ad_zone_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/ad/zone/list',
		'defaults' => array(
			'module'	 => 'ad',
			'controller' => 'zone',
			'action'	 => 'list',
		),
	),
);
