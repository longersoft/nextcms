<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	configs
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing private message folders
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new folder
	'message_folder_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/folder/add',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'folder',
			'action'	 => 'add',
			'allowed'	 => true,
		),
	),
	
	// Delete folder
	'message_folder_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/folder/delete',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'folder',
			'action'	 => 'delete',
			'allowed'	 => true,
		),
	),

	// List folders
	'message_folder_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/folder/list',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'folder',
			'action'	 => 'list',
			'allowed'	 => true,
		),
	),
	
	// Rename folder
	'message_folder_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/folder/rename',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'folder',
			'action'	 => 'rename',
			'allowed'	 => true,
		),
	),
);
