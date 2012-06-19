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
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Emoticon hook
 * 
 * @return array
 */
return array(
	'name'  => 'emoticon',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Emoticon',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Replace special characters with emoticons',
	),
	'thumbnail' => '/modules/util/hooks/emoticon/emoticon.png',
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
		'skin' => 'small',
		'maps' => array(
			// See http://en.wikipedia.org/wiki/List_of_emoticons
			'Angel'		  => array('O:)', 'O:-)'),
			'Confused'	  => array('o.O', 'O.o'),
			'Cry'		  => array(":'("),
			'Devil'		  => array('3:)', '3:-)'),
			'Embarrassed' => array('>:X', ':-X', ':X', ':-#', ':#', ':$'),
			'Glasses'	  => array('8-)', '8)', 'B-)', 'B)'),
			'Grin'		  => array(':-D', ':D', '=D'),
			'Kiss'		  => array(':-*', ':*'),
			'Laugh'		  => array('>:D'),
			'Raspberry'   => array('>:P', 'X-P', 'x-p'),
			'Sad'		  => array(':-(', ':(', ':[', '=('),
			'Sleeping'	  => array('ZZzzz...'),	
			'Smile'		  => array(':-)', ':)', ':]', '=)'),
			'Surprise'	  => array('>:o', '>:O', ':-O', ':O'),
			'Tongue'	  => array(':-P', ':P', ':-p', ':p', '=P'),
			'Unsure'	  => array(':-/', ':\\', ':-\\'),
			'Wink'		  => array(';-)', ';)'),
		),
	),
	'filter'  => true,
	'install' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
	'uninstall' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
);
