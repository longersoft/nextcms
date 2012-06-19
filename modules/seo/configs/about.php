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
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Seo module
 * 
 * @return array
 */
return array(
	'name'  => 'seo',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'SEO',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Provides SEO utilities',
	),
	'icon'		=> '/modules/seo/images/seo16.png',
	'thumbnail'	=> '/modules/seo/images/seo32.png',
	'website'	=> null,
	'author'	=> 'Nguyen Huu Phuoc',
	'email'		=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'modules'		=> '',
		'phpExtensions' => '',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> ''
	),
	'backendMenu' => array(
		'seo_sitemap_build' => array(
			'icon'			 => '/modules/seo/images/sitemap16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.sitemap.build',
			'description'	 => 'Sitemap Builder',
		),
	),
	'install' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
	'uninstall' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
);
