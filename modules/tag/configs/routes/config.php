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
 * Define routes for setting the Tag module
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Configure the Tag module
	'tag_config_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/config/config',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'config',
			'action'	 => 'config',
		),
	),
);
