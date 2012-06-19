<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing categories
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new category
	'category_category_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/category/add',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'category',
			'action'	 => 'add',
		),
	),
	
	// Delete category
	'category_category_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/category/delete',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'category',
			'action'	 => 'delete',
		),
	),
	
	// Edit category
	'category_category_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/category/edit',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'category',
			'action'	 => 'edit',
		),
	),
	
	// List categories
	'category_category_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/category/list',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'category',
			'action'	 => 'list',
		),
	),
	
	// Move category
	'category_category_move' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/category/move',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'category',
			'action'	 => 'move',
		),
	),
	
	// Rename category
	'category_category_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/category/rename',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'category',
			'action'	 => 'rename',
		),
	),
);
