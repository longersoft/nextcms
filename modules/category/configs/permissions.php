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
 * Define the permissions of the Category module
 * 
 * @return array
 */
return array(
	// Categories manager
	'category' => array(
		'translationKey' => '_permission.category.description',
		'description'	 => 'Manage categories',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.category.actions.add',
				'description'	 => 'Add new category',
			),
			'delete' => array(
				'translationKey' => '_permission.category.actions.delete',
				'description'	 => 'Delete category',
			),
			'edit' => array(
				'translationKey' => '_permission.category.actions.edit',
				'description'	 => 'Edit category',
			),
			'list' => array(
				'translationKey' => '_permission.category.actions.list',
				'description'	 => 'List categories',
			),
			'move' => array(
				'translationKey' => '_permission.category.actions.move',
				'description'	 => 'Move category',
			),
			'rename' => array(
				'translationKey' => '_permission.category.actions.rename',
				'description'	 => 'Rename category',
			),
		),
	),
	
	// Folders manager
	'folder' => array(
		'translationKey' => '_permission.folder.description',
		'description'	 => 'Manage folders',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.folder.actions.add',
				'description'	 => 'Add new folder',
			),
			'delete' => array(
				'translationKey' => '_permission.folder.actions.delete',
				'description'	 => 'Delete folder',
			),
			'list' => array(
				'translationKey' => '_permission.folder.actions.list',
				'description'	 => 'List folders',
			),
			'rename' => array(
				'translationKey' => '_permission.folder.actions.rename',
				'description'	 => 'Rename folder',
			),
		),
	),
);
