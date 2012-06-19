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
 * Define the permissions of the Message module
 * 
 * @return array
 */
return array(
	// Config module
	'config' => array(
		'translationKey' => '_permission.config.description',
		'description'	 => 'Configure module',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.config.actions.config',
				'description'	 => 'Configure module',
			),
		),
	),
);
