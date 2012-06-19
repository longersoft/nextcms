<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	configs
 * @since		1.0
 * @version		2012-01-13
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Tag module
 * 
 * @return array
 */
return array(
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

	// Manage tags
	'tag' => array(
		'translationKey' => '_permission.tag.description',
		'description'	 => 'Manage tags',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.tag.actions.add',
				'description'	 => 'Add new tag',
			),
			'delete' => array(
				'translationKey' => '_permission.tag.actions.delete',
				'description'	 => 'Delete a tag',
			),
			'edit' => array(
				'translationKey' => '_permission.tag.actions.edit',
				'description'	 => 'Edit a tag',
			),
			'list' => array(
				'translationKey' => '_permission.tag.actions.list',
				'description'	 => 'List tags',
			),
		),
	),
);
