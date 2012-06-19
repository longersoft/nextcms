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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the VideoProvider hook
 * 
 * @return array
 */
return array(
	'name'  => 'videoprovider',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Videos Provider',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Search for videos and allow to drag and drop the videos to other targets',
	),
	'thumbnail' => '/modules/media/hooks/videoprovider/videoprovider32.png',
	'website'   => null,
	'author'    => 'Nguyen Huu Phuoc',
	'email' 	=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'phpExtensions' => '',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> 'mysql,pdo_mysql',
	),
	'options' => null,
	'targets' => array(
		array(
			'module' => 'core',
			'name'	 => 'Core_Layout_Admin_ShowVideoToolbox',
			'method' => 'show',
			'params' => null,
			'echo'	 => true,
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
