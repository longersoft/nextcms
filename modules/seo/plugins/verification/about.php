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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-12-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Verification plugin
 * 
 * @return array
 */
return array(
	'name'  => 'verification',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Website verification',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Insert website verification codes to the head section',
	),
	'thumbnail' => '/modules/seo/plugins/verification/verification.png',
	'website'   => null,
	'author'    => 'Nguyen Huu Phuoc',
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
		'google_code' => '',
		'bing_code'	  => '',
		'alexa_code'  => '',
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
