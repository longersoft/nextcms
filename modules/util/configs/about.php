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
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Util module
 * 
 * @return array
 */
return array(
	'name'  => 'util',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Utility',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Provides utilities',
	),
	'icon'		=> '/modules/util/images/util16.png',
	'thumbnail'	=> '/modules/util/images/util32.png',
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
	'backendMenu' => array(),
	'install'	  => array(
		'queries'	=> array(),
		'callbacks' => array(
			'Util_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries'	=> array(),
		'callbacks' => array(
			'Util_Services_Installer::uninstallModule',
		),
	),
);
