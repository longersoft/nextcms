<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the File module
 * 
 * @return array
 */
return array(
	'name'  => 'file',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'File',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'This module is used to manage files',
	),
	'icon'		=> '/modules/file/images/file16.png',
	'thumbnail' => '/modules/file/images/file32.png',
	'website'	=> null,
	'author'	=> 'Nguyen Huu Phuoc',
	'email'		=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'modules'		=> '',
		'phpExtensions' => 'gd|imagick',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> ''
	),
	'backendMenu' => array(
		'file_attachment_list' => array(
			'icon'			 => '/modules/file/images/attachment16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.attachment.list',
			'description'	 => 'Manage attachments',
		),
		'file_explorer_list' => array(
			'icon'			 => '/modules/file/images/explorer16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.explorer.list',
			'description'	 => 'File explorer',
		),
		'file_config_config' => array(
			'icon'			 => '/modules/file/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###file_attachment`",
				"CREATE TABLE `###file_attachment` (
					`attachment_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`hash` VARCHAR(255) NOT NULL,
					`title` VARCHAR(255) NOT NULL,
					`slug` VARCHAR(255) NULL DEFAULT NULL,
					`description` TEXT NULL,
					`name` VARCHAR(255) NOT NULL,
					`extension` VARCHAR(20) NOT NULL,
					`path` TEXT NOT NULL,
					`size` INT(10) UNSIGNED NOT NULL,
					`uploaded_user` INT(10) UNSIGNED NOT NULL,
					`uploaded_date` DATETIME NOT NULL,
					`num_downloads` INT(10) UNSIGNED NULL DEFAULT '0',
					`last_download` DATETIME NULL DEFAULT NULL,
					`auth_required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					`password` VARCHAR(50) NULL DEFAULT NULL,
					`language` VARCHAR(10) NULL DEFAULT NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`attachment_id`),
					UNIQUE INDEX `idx_hash` (`hash`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###file_bookmark`",
				"CREATE TABLE `###file_bookmark` (
					`bookmark_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`connection_id` INT(10) UNSIGNED NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`path` TEXT NOT NULL,
					PRIMARY KEY (`bookmark_id`),
					INDEX `idx_connection_id` (`connection_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###file_connection`;",
				"CREATE TABLE `###file_connection` (
					`connection_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`type` ENUM('local', 'ftp') NOT NULL DEFAULT 'local',
					`name` VARCHAR(200) NOT NULL,
					`server` VARCHAR(255) NULL DEFAULT NULL,
					`port` VARCHAR(20) NULL DEFAULT NULL,
					`user_name` VARCHAR(50) NULL DEFAULT NULL,
					`password` VARCHAR(50) NULL DEFAULT NULL,
					`init_path` VARCHAR(255) NULL DEFAULT NULL,
					PRIMARY KEY (`connection_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'File_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###file_attachment`",
				"DROP TABLE IF EXISTS `###file_bookmark`;",
				"DROP TABLE IF EXISTS `###file_connection`;",
			),
		),
		'callbacks' => array(
			'File_Services_Installer::uninstallModule',
		),
	),
);
