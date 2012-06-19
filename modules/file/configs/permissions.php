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
 * Define the permissions of the File module
 * 
 * @return array
 */
return array(
	// Manage attachments
	'attachment' => array(
		'translationKey' => '_permission.attachment.description',
		'description' 	 => 'Manage attachments',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.attachment.actions.add',
				'description'	 => 'Add new attachment',
			),
			'delete' => array(
				'translationKey' => '_permission.attachment.actions.delete',
				'description'	 => 'Delete attachment',
			),
			'edit' => array(
				'translationKey' => '_permission.attachment.actions.edit',
				'description'	 => 'Update attachment',
			),
			'list' => array(
				'translationKey' => '_permission.attachment.actions.list',
				'description'	 => 'List attachments',
			),
		),
	),

	// Manage bookmarks
	'bookmark' => array(
		'translationKey' => '_permission.bookmark.description',
		'description'	 => 'Manage bookmarks',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.bookmark.actions.add',
				'description'	 => 'Add new bookmark',
			),
			'delete' => array(
				'translationKey' => '_permission.bookmark.actions.delete',
				'description'	 => 'Delete bookmark',
			),
			'list' => array(
				'translationKey' => '_permission.bookmark.actions.list',
				'description'	 => 'List bookmarks',
			),
			'rename' => array(
				'translationKey' => '_permission.bookmark.actions.rename',
				'description'	 => 'Rename bookmark',
			),
		),
	),
	
	// Config module
	'config' => array(
		'translationKey' => '_permission.config.description',
		'description'	 => 'Configure module',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.config.actions.config',
				'description'	 => 'Configure module',
			),
		),
	),

	// Manage connections
	'connection' => array(
		'translationKey' => '_permission.connection.description',
		'description' 	 => 'Manage connections',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.connection.actions.add',
				'description'	 => 'Add new connection',
			),
			'connect' => array(
				'translationKey' => '_permission.connection.actions.connect',
				'description'	 => 'Connect the connection',
			),
			'delete' => array(
				'translationKey' => '_permission.connection.actions.delete',
				'description'	 => 'Delete connection',
			),
			'disconnect' => array(
				'translationKey' => '_permission.connection.actions.disconnect',
				'description'	 => 'Disconnect the connection',
			),
			'edit' => array(
				'translationKey' => '_permission.connection.actions.edit',
				'description'	 => 'Update connection',
			),
			'list' => array(
				'translationKey' => '_permission.connection.actions.list',
				'description'	 => 'List connections',
			),
			'rename' => array(
				'translationKey' => '_permission.connection.actions.rename',
				'description'	 => 'Rename connection',
			),
		),
	),

	// File explorer
	'explorer' => array(
		'translationKey' => '_permission.explorer.description',
		'description' 	 => 'File explorer',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.explorer.actions.add',
				'description'	 => 'Add new directory',
			),
			'compress' => array(
				'translationKey' => '_permission.explorer.actions.compress',
				'description'	 => 'Compress file',
			),
			'copy' => array(
				'translationKey' => '_permission.explorer.actions.copy',
				'description'	 => 'Copy file',
			),
			'delete' => array(
				'translationKey' => '_permission.explorer.actions.delete',
				'description'	 => 'Delete file',
			),
			'download' => array(
				'translationKey' => '_permission.explorer.actions.download',
				'description'	 => 'Download file',
			),
			'edit' => array(
				'translationKey' => '_permission.explorer.actions.edit',
				'description'	 => 'Edit file',
			),
			'extract' => array(
				'translationKey' => '_permission.explorer.actions.extract',
				'description'	 => 'Extract compressed file',
			),
			'list' => array(
				'translationKey' => '_permission.explorer.actions.list',
				'description'	 => 'List files',
			),
			'move' => array(
				'translationKey' => '_permission.explorer.actions.move',
				'description'	 => 'Move file',
			),
			'perm' => array(
				'translationKey' => '_permission.explorer.actions.perm',
				'description'	 => 'Set file permissions',
			),
			'rename' => array(
				'translationKey' => '_permission.explorer.actions.rename',
				'description'	 => 'Rename file',
			),
			'upload' => array(
				'translationKey' => '_permission.explorer.actions.upload',
				'description'	 => 'Upload files',
			),
			'view' => array(
				'translationKey' => '_permission.explorer.actions.view',
				'description'	 => 'View file',
			),
		),
	),

	// Manage files in local directory
	'file' => array(
		'translationKey' => '_permission.file.description',
		'description'	 => 'Manage files',
		'actions'		 => array(
			'upload' => array(
				'translationKey' => '_permission.file.actions.upload',
				'description'	 => 'Upload files',
			),
		),
	),
);
