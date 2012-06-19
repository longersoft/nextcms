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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Twitter Updater hook
 * 
 * @return array
 */
return array(
	'name'  => 'twitterupdater',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Twitter Updater',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Post new status about new article, photo, video, etc to Twitter after publishing them',
	),
	'thumbnail' => '/modules/util/hooks/twitterupdater/twitterupdater.png',
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
	'options' => array(),
	'targets' => array(
		array(
			'module' => 'content',
			'name'	 => 'Content_Activate_Article',
			'method' => 'post',
			'params' => null,
			'echo'	 => false,
		),
		array(
			'module' => 'media',
			'name'	 => 'Media_Activate_Album',
			'method' => 'post',
			'params' => null,
			'echo'	 => false,
		),
		array(
			'module' => 'media',
			'name'	 => 'Media_Activate_Photo',
			'method' => 'post',
			'params' => null,
			'echo'	 => false,
		),
		array(
			'module' => 'media',
			'name'	 => 'Media_Activate_Playlist',
			'method' => 'post',
			'params' => null,
			'echo'	 => false,
		),
		array(
			'module' => 'media',
			'name'	 => 'Media_Activate_Video',
			'method' => 'post',
			'params' => null,
			'echo'	 => false,
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
