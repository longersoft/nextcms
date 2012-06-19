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
 * Define routes for managing files/folders
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Create new directory
	'file_explorer_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/add',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'add',
		),
	),
	
	// Compress file
	'file_explorer_compress' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/compress',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'compress',
		),
	),
	
	// Copy files
	'file_explorer_copy' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/copy',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'copy',
		),
	),
	
	// Delete file
	'file_explorer_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/delete',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'delete',
		),
	),
	
	// Download file
	'file_explorer_download' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/download',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'download',
		),
	),
	
	// Edit file
	'file_explorer_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/edit',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'edit',
		),
	),
	
	// Extract compressed file
	'file_explorer_extract' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/extract',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'extract',
		),
	),
	
	// List files
	'file_explorer_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/list',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'list',
		),
	),
	
	// Move file to other directory
	'file_explorer_move' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/move',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'move',
		),
	),
	
	// Set file permissions
	'file_explorer_perm' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/perm',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'perm',
		),
	),
	
	// Rename file
	'file_explorer_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/rename',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'rename',
		),
	),
	
	// Upload file
	'file_explorer_upload' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/upload',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'upload',
		),
	),
	
	// View file (text, image files)
	'file_explorer_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/explorer/view',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'explorer',
			'action'	 => 'view',
		),
	),
);
