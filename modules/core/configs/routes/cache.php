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
 * @version		2011-11-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing cache
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Clean all cache
	'core_cache_clean' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/cache/clean',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'cache',
			'action'	 => 'clean',
		),
	),

	// Set the page cache lifetime
	'core_cache_page' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/cache/page',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'cache',
			'action'	 => 'page',
		),
	),
	
	// Remove cache
	'core_cache_remove' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/cache/remove',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'cache',
			'action'	 => 'remove',
		),
	),
);
