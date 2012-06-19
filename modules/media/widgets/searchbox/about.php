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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-16
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the SearchBox widget
 * 
 * @return array
 */
return array(
	'name'  => 'searchbox',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	=> 'Search Box',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Show a search box',
	),
	'thumbnail' => '/modules/media/widgets/searchbox/searchbox.png',
	'website'   => null,
	'author'	=> 'Nguyen Huu Phuoc',
	'email'		=> 'thenextcms@gmail.com',
	'version'	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'phpExtensions' => '',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> '',
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
