<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for sending new password to user
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Generate password
	'core_password_generate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/password/generate',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'password',
			'action'	 => 'generate',
		),
	),

	// Send new password
	'core_password_send' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/password/send',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'password',
			'action'	 => 'send',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'password.send.title',
			),
		),
	),
);
