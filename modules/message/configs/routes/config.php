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
 * Define routes for setting the Message module
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Configure the Message module
	'message_config_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/config/config',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'config',
			'action'	 => 'config',
		),
	),
);
