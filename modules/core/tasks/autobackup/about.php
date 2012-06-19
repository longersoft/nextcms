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
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Autobackup task
 * 
 * @return array
 */
return array(
	'name'  => 'autobackup',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	 => 'Auto Backup',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Backup the MySQL database automatically',
	),
	'thumbnail' => '/modules/core/tasks/autobackup/autobackup32.png',
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
	'timeMask' => '0 0 * * *',
	'options'  => null,
	'actions'  => array(
		'view' => array(
			'translationKey' => '_permission.actions.view',
			'description' 	 => 'View SQL files',
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
