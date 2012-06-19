<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing folder bookmarks
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new bookmark
	'file_bookmark_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/bookmark/add',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'bookmark',
			'action'	 => 'add',
		),
	),
	
	// Delete bookmark
	'file_bookmark_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/bookmark/delete',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'bookmark',
			'action'	 => 'delete',
		),
	),
	
	// List bookmarks
	'file_bookmark_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/bookmark/list',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'bookmark',
			'action'	 => 'list',
		),
	),
	
	// Rename bookmark
	'file_bookmark_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/bookmark/rename',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'bookmark',
			'action'	 => 'rename',
		),
	),
);
