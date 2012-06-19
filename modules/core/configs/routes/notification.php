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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for showing notifications
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Log error
	'core_notification_log' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/notification/log',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'notification',
			'action'	 => 'log',
		),
	),
	
	// Show the notification
	'core_notification_show' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/notification/show',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'notification',
			'action'	 => 'show',
		),
	),
);
