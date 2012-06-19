<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	configs
 * @since		1.0
 * @version		2012-05-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Content module
 * 
 * @return array
 */
return array(
	// Manage articles
	'article' => array(
		'translationKey' => '_permission.article.description',
		'description'	 => 'Manage articles',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.article.actions.activate',
				'description'	 => 'Activate/deactivate article',
			),
			'add' => array(
				'translationKey' => '_permission.article.actions.add',
				'description'	 => 'Add new article',
			),
			'cover' => array(
				'translationKey' => '_permission.article.actions.cover',
				'description'	 => 'Update cover',
			),
			'delete' => array(
				'translationKey' => '_permission.article.actions.delete',
				'description'	 => 'Delete article',
			),
			'edit' => array(
				'translationKey' => '_permission.article.actions.edit',
				'description'	 => 'Edit article',
			),
			'empty' => array(
				'translationKey' => '_permission.article.actions.empty',
				'description'	 => 'Empty trash',
			),
			'list' => array(
				'translationKey' => '_permission.article.actions.list',
				'description'	 => 'List articles',
			),
			'move' => array(
				'translationKey' => '_permission.article.actions.move',
				'description'	 => 'Move articles',
			),
			'order' => array(
				'translationKey' => '_permission.article.actions.order',
				'description'	 => 'Order articles',
			),
		),
	),
	
	// Manage articles in folders
	'folder' => array(
		'translationKey' => '_permission.folder.description',
		'description'	 => 'Manage articles in folders',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.folder.actions.add',
				'description'	 => 'Add an article to a folder',
			),
			'remove' => array(
				'translationKey' => '_permission.folder.actions.remove',
				'description'	 => 'Remove an article from a folder',
			),
		),
	),
	
	// Manage revisions
	'revision' => array(
		'translationKey' => '_permission.revision.description',
		'description'	 => 'Manage revisions',
		'actions'		 => array(
			'delete' => array(
				'translationKey' => '_permission.revision.actions.delete',
				'description'	 => 'Delete revision',
			),
			'list' => array(
				'translationKey' => '_permission.revision.actions.list',
				'description'	 => 'List revisions',
			),
			'restore' => array(
				'translationKey' => '_permission.revision.actions.restore',
				'description'	 => 'Restore revision',
			),
			'view' => array(
				'translationKey' => '_permission.revision.actions.view',
				'description'	 => 'View revision details',
			),
		),
	),
);
