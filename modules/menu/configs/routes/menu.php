<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing menus
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new menu
	'menu_menu_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/menu/menu/add',
		'defaults' => array(
			'module'	 => 'menu',
			'controller' => 'menu',
			'action'	 => 'add',
		),
	),

	// Delete menu
	'menu_menu_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/menu/menu/delete',
		'defaults' => array(
			'module'	 => 'menu',
			'controller' => 'menu',
			'action'	 => 'delete',
		),
	),
	
	// Edit menu
	'menu_menu_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/menu/menu/edit',
		'defaults' => array(
			'module'	 => 'menu',
			'controller' => 'menu',
			'action'	 => 'edit',
		),
	),
	
	// List menus
	'menu_menu_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/menu/menu/list',
		'defaults' => array(
			'module'	 => 'menu',
			'controller' => 'menu',
			'action'	 => 'list',
		),
	),
);
