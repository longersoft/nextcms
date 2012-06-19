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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the EmailObfuscator plugin
 * 
 * @return array
 */
return array(
	'name'  => 'emailobfuscator',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Email Obfuscator',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Encode all email address on page',
	),
	'thumbnail' => '/modules/util/plugins/emailobfuscator/emailobfuscator.png',
	'website'   => null,
	'author'    => 'Nguyen Huu Phuoc',
	'email' 	=> null,
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'phpExtensions' => '',
		'php'			=> '5.2+',
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
