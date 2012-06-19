<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	configs
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Message module
 * 
 * @return array
 */
return array(
	'name'  => 'message',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Private Messsage',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Manage private messages',
	),
	'icon'		=> '/modules/message/images/message16.png',
	'thumbnail' => '/modules/message/images/message32.png',
	'website'   => null,
	'author'    => 'Nguyen Huu Phuoc',
	'email' 	=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'modules'       => '',
		'phpExtensions' => '',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> 'mysql,pdo_mysql'
	),
	'backendMenu' => array(
		'message_config_config' => array(
			'icon'			 => '/modules/message/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###message`;",
				"CREATE TABLE `###message` (
					`message_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`root_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`sent_user` INT(10) UNSIGNED NOT NULL,
					`subject` VARCHAR(255) NOT NULL,
					`content` TEXT NOT NULL,
					`sent_date` DATETIME NOT NULL,
					`to_address` TEXT NOT NULL,
					`bcc_address` TEXT NULL,
					`reply_to` INT(11) UNSIGNED NULL DEFAULT '0',
					`attachments` TEXT NULL,
					PRIMARY KEY (`message_id`),
					INDEX `idx_root_id` (`root_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###message_attachment`;",
				"CREATE TABLE `###message_attachment` (
					`attachment_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`message_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`path` VARCHAR(255) NOT NULL,
					`name` VARCHAR(255) NOT NULL,
					`extension` VARCHAR(50) NULL DEFAULT NULL,
					`size` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`last_download_user` INT(10) UNSIGNED NULL DEFAULT '0',
					`last_download_date` DATETIME NULL DEFAULT NULL,
					`num_downloads` INT(10) UNSIGNED NULL DEFAULT '0',
					PRIMARY KEY (`attachment_id`),
					INDEX `idx_message_id` (`message_id`),
					INDEX `idx_path` (`path`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###message_filter`;",
				"CREATE TABLE `###message_filter` (
					`filter_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`object` ENUM('subject','content','from') NOT NULL DEFAULT 'subject',
					`condition` ENUM('like','not_like','is','not','begin','end') NOT NULL,
					`comparison_to` VARCHAR(255) NOT NULL,
					`actions` TEXT NOT NULL,
					`folder_id` VARCHAR(10) NULL,
					PRIMARY KEY (`filter_id`),
					INDEX `idx_user_id` (`user_id`),
					INDEX `idx_user_folder` (`user_id`, `folder_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###message_folder`;",
				"CREATE TABLE `###message_folder` (
					`folder_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`name` VARCHAR(100) NOT NULL,
					`num_messages` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (`folder_id`),
					INDEX `idx_user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###message_recipient`;",
				"CREATE TABLE `###message_recipient` (
					`message_id` INT(10) UNSIGNED NOT NULL,
					`root_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`from_user` INT(10) UNSIGNED NOT NULL,
					`to_user` INT(10) UNSIGNED NOT NULL,
					`folder_id` VARCHAR(40) NULL DEFAULT NULL,
					`deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					`unread` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
					`starred` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					INDEX `idx_message_id` (`message_id`),
					INDEX `idx_user_folder` (`to_user`, `folder_id`),
					INDEX `idx_root_id` (`root_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Message_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries'	=> array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###message`",
				"DROP TABLE IF EXISTS `###message_attachment`;",
				"DROP TABLE IF EXISTS `###message_filter`;",
				"DROP TABLE IF EXISTS `###message_folder`;",
				"DROP TABLE IF EXISTS `###message_recipient`;",
			),
		),
		'callbacks' => array(
			'Message_Services_Installer::uninstallModule',
		),
	),
);
