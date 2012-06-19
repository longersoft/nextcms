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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Debug plugin
 * 
 * @return array
 */
return array(
	'name'  => 'debug',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Debug',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Show useful information for debugging. Based on the ZFDebug created by Joakim Nygard and Andreas Pankratz',
	),
	'thumbnail' => '/modules/core/plugins/debug/debug.png',
	'website'   => null,
	'author'    => 'Joakim Nygard <http://jokke.dk>, Andreas Pankratz <http://www.bangal.de>, Nguyen Huu Phuoc <thenextcms@gmail.com>',
	'email' 	=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'phpExtensions' => '',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> '',
	),
	'options' => array(
		'plugins' => array('variable', 'registry', 'system'),
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
