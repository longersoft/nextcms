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
 * Define routes for managing connections
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new connection
	'file_connection_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/add',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'add',
		),
	),
	
	// Connect
	'file_connection_connect' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/connect',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'connect',
		),
	),
	
	// Delete connection
	'file_connection_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/delete',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'delete',
		),
	),
	
	// Disconnet
	'file_connection_disconnect' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/disconnect',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'disconnect',
		),
	),
	
	// Edit connection
	'file_connection_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/edit',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'edit',
		),
	),
	
	// List connections
	'file_connection_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/list',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'list',
		),
	),
	
	// Rename connection
	'file_connection_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/connection/rename',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'connection',
			'action'	 => 'rename',
		),
	),
);
