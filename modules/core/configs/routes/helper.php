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
 * @version		2012-05-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for helper actions, such as generating slugs, etc.
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Embed a video
	'core_helper_play' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/helper/play',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'helper',
			'action'	 => 'play',
		),
	),

	////////// BACKEND ACTIONS //////////
	// Generate slug
	'core_helper_slug' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/helper/slug',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'helper',
			'action'	 => 'slug',
			'allowed'	 => true,
		),
	),
);
