<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	configs
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for sitemaps
 * 
 * @return array
 */
return array(
	// View articles sitemap
	'content_sitemap_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'content/sitemap/articles.xml',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'sitemap',
			'action'	 => 'index',
		),
	),

	// View articles sitemap by year and month
	'content_sitemap_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/sitemap/articles-(\d+)-(\d+).xml',
		'reverse'  => 'content/sitemap/articles-%s-%s.xml',
		'map'	   => array(
			'1' => 'year',
			'2' => 'month',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'sitemap',
			'action'	 => 'view',
		),
	),
);
