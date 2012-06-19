<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Poll module
 * 
 * @return array
 */
return array(
	'name'  => 'poll',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Poll',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Manage polls',
	),
	'icon'		=> '/modules/poll/images/poll16.png',
	'thumbnail'	=> '/modules/poll/images/poll32.png',
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
		'dbAdapters'	=> 'mysql,pdo_mysql'
	),
	'backendMenu' => array(
		'poll_poll_list' => array(
			'icon'			 => '/modules/poll/images/poll16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.poll.list',
			'description'	 => 'Manage polls',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				// FIXME: Create an index of translations column
				"DROP TABLE IF EXISTS `###poll`;",
				"CREATE TABLE `###poll` (
					`poll_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NULL,
					`created_user` INT(11) UNSIGNED NOT NULL,
					`created_date` DATETIME NOT NULL,
					`multiple_options` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					`language` VARCHAR(10) NULL DEFAULT NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`poll_id`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###poll_option`;",
				"CREATE TABLE `###poll_option` (
					`option_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`poll_id` INT(11) UNSIGNED NOT NULL,
					`ordering` INT(11) UNSIGNED NOT NULL,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NULL,
					`num_choices` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (`option_id`),
					INDEX `idx_poll_id` (`poll_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Poll_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###poll`;",
				"DROP TABLE IF EXISTS `###poll_option`;",
			),
		),
		'callbacks' => array(
			'Poll_Services_Installer::uninstallModule',
		),
	),
);
