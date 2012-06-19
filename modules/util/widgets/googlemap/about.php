<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		util
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-01-02
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the GoogleMap widget
 * 
 * @return array
 */
return array(
	'name'  => 'googlemap',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	=> 'Google Map',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Display location of an address on Google Map',
	),
	'thumbnail' => '/modules/util/widgets/googlemap/googlemap.png',
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
