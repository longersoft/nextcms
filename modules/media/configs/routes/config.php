<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	configs
 * @since		1.0
 * @version		2011-11-04
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for setting the Media module
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Configure the module
	'media_config_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/media/config/config',
		'defaults' => array(
			'module'	 => 'media',
			'controller' => 'config',
			'action'	 => 'config',
		),
	),
);
