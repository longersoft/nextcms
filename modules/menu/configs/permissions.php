<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Menu module
 * 
 * @return array
 */
return array(
	// Menus manager
	'menu' => array(
		'translationKey' => '_permission.menu.description',
		'description'	 => 'Manage menus',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.menu.actions.add',
				'description'	 => 'Add new menu',
			),
			'delete' => array(
				'translationKey' => '_permission.menu.actions.delete',
				'description'	 => 'Delete menu',
			),
			'edit' => array(
				'translationKey' => '_permission.menu.actions.edit',
				'description'	 => 'Edit menu',
			),
			'list' => array(
				'translationKey' => '_permission.menu.actions.list',
				'description'	 => 'List menus',
			),
		),
	),
);
