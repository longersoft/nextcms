<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		seo
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing sitemap
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Build sitemap
	'seo_sitemap_build' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/seo/sitemap/build',
		'defaults' => array(
			'module'	 => 'seo',
			'controller' => 'sitemap',
			'action'	 => 'build',
		),
	),
);
