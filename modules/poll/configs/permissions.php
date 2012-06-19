<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Poll module
 * 
 * @return array
 */
return array(
	// Polls manager
	'poll' => array(
		'translationKey' => '_permission.poll.description',
		'description'	 => 'Manage polls',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.poll.actions.add',
				'description'	 => 'Add new poll',
			),
			'delete' => array(
				'translationKey' => '_permission.poll.actions.delete',
				'description'	 => 'Delete poll',
			),
			'edit' => array(
				'translationKey' => '_permission.poll.actions.edit',
				'description'	 => 'Edit poll',
			),
			'list' => array(
				'translationKey' => '_permission.poll.actions.list',
				'description'	 => 'List polls',
			),
		),
	),
);
