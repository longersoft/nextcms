<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		util
 * @subpackage	hooks
 * @since		1.0
 * @version		2011-12-09
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Emoticon hook
 * 
 * @return array
 */
return array(
	'emoticon' => array(
		'translationKey' => '_permission.description',
		'description'	 => 'Replace special characters with emoticons',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.actions.config',
				'description'	 => 'Show the setting form',
			),
			'save' => array(
				'translationKey' => '_permission.actions.save',
				'description'	 => 'Save the settings',
			),
		),
	),
);
