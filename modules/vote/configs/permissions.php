<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		vote
 * @subpackage	configs
 * @since		1.0
 * @version		2012-03-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Vote module
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
);
