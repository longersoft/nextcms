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
 * Information of the Editor hook
 * 
 * @return array
 */
return array(
	'name'  => 'editor',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Image editor',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Provide a toolbox allowing to upload and edit image',
	),
	'thumbnail' => '/modules/media/hooks/editor/editor32.png',
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
	'options' => null,
	'targets' => array(
		array(
			'module' => 'core',
			'name'	 => 'Core_Layout_Admin_ShowImageToolbox',
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
