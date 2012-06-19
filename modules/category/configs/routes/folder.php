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
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing folders
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new folder
	'category_folder_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/folder/add',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'folder',
			'action'	 => 'add',
		),
	),
	
	// Delete folder
	'category_folder_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/folder/delete',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'folder',
			'action'	 => 'delete',
		),
	),
	
	// List folders
	'category_folder_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/folder/list',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'folder',
			'action'	 => 'list',
		),
	),
	
	// Rename folder
	'category_folder_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/category/folder/rename',
		'defaults' => array(
			'module'	 => 'category',
			'controller' => 'folder',
			'action'	 => 'rename',
		),
	),
);
