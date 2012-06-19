<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Comment module
 * 
 * @return array
 */
return array(
	'name'  => 'comment',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Comment',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Manage comments',
	),
	'icon'		=> '/modules/comment/images/comment16.png',
	'thumbnail' => '/modules/comment/images/comment32.png',
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
		'comment_comment_list' => array(
			'icon'			 => '/modules/comment/images/comment16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.comment.list',
			'description'	 => 'Manage comments',
		),
		'comment_config_config' => array(
			'icon'			 => '/modules/comment/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###comment`;",
				"CREATE TABLE `###comment` (
					`comment_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`entity_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`entity_class` VARCHAR(200) NOT NULL,
					`entity_module` VARCHAR(50) NOT NULL,
					`title` VARCHAR(255) NOT NULL,
					`content` TEXT NOT NULL,
					`full_name` VARCHAR(255) NULL DEFAULT NULL,
					`web_site` VARCHAR(255) NULL DEFAULT NULL,
					`email` VARCHAR(100) NULL DEFAULT NULL,
					`ip` VARCHAR(50) NULL DEFAULT NULL,
					`user_agent` TEXT NULL,
					`created_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`created_date` DATETIME NOT NULL,
					`activated_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`activated_date` DATETIME NULL DEFAULT NULL,
					`status` ENUM('activated','not_activated','spam') NOT NULL DEFAULT 'not_activated',
					`path` VARCHAR(255) NULL DEFAULT NULL,
					`ordering` INT(11) UNSIGNED NULL DEFAULT '0',
					`depth` INT(11) UNSIGNED NULL DEFAULT '0',
					`reply_to` INT(11) UNSIGNED NULL DEFAULT '0',
					`language` VARCHAR(10) NULL DEFAULT NULL,
					`num_ups` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`num_downs` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (`comment_id`),
					INDEX `idx_entity` (`entity_id`, `entity_class`, `entity_module`),
					INDEX `idx_ordering` (`ordering`),
					INDEX `idx_path` (`path`),
					INDEX `idx_status` (`status`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Comment_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###comment`;",
			),
		),
		'callbacks' => array(
			'Comment_Services_Installer::uninstallModule',
		),
	),
);
