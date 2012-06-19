<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Comment module
 * 
 * @return array
 */
return array(
	// Manage comments
	'comment' => array(
		'translationKey' => '_permission.comment.description',
		'description'	 => 'Manage comments',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.comment.actions.activate',
				'description'	 => 'Activate/deactivate comment',
			),
			'delete' => array(
				'translationKey' => '_permission.comment.actions.delete',
				'description'	 => 'Delete comment',
			),
			'edit' => array(
				'translationKey' => '_permission.comment.actions.edit',
				'description'	 => 'Edit comment',
			),
			'list' => array(
				'translationKey' => '_permission.comment.actions.list',
				'description'	 => 'List comments',
			),
			'reply' => array(
				'translationKey' => '_permission.comment.actions.reply',
				'description'	 => 'Reply to a comment',
			),
			'spam' => array(
				'translationKey' => '_permission.comment.actions.spam',
				'description'	 => 'Report spam',
			),
			'view' => array(
				'translationKey' => '_permission.comment.actions.view',
				'description'	 => 'View comment thread',
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
);
