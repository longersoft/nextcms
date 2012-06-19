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
 * @version		2012-03-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Minifier plugin
 * 
 * @return array
 */
return array(
	'name'  => 'minifier',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Minifier',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Minify the HTML content of page',
	),
	'thumbnail' => '/modules/util/plugins/minifier/minifier.png',
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
	'options' => null,
	'install' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
	'uninstall' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
);
