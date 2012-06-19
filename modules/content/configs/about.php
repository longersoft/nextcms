<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	configs
 * @since		1.0
 * @version		2012-05-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Content module
 * 
 * @return array
 */
return array(
	'name'  => 'content',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Content',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Manage content',
	),
	'icon'		=> '/modules/content/images/content16.png',
	'thumbnail' => '/modules/content/images/content32.png',
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
		'content_article_list' => array(
			'icon'			 => '/modules/content/images/content16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.article.list',
			'description'	 => 'Manage articles',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###content_article`;",
				"CREATE TABLE `###content_article` (
					`article_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`category_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`categories` TEXT NULL,
					`type` ENUM('article','page','blog') NOT NULL DEFAULT 'article',
					`title` VARCHAR(255) NOT NULL,
					`sub_title` VARCHAR(255) NULL DEFAULT NULL,
					`slug` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`meta_description` TEXT NULL,
					`meta_keyword` TEXT NULL,
					`content` TEXT NOT NULL,
					`layout` TEXT NOT NULL,
					`user_name` VARCHAR(100) NULL DEFAULT NULL,
					`author` VARCHAR(255) NOT NULL,
					`credit` VARCHAR(255) NULL DEFAULT NULL,
					`featured` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`image_icon` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`video_icon` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`ordering` INT(10) UNSIGNED NULL DEFAULT '0',
					`num_comments` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`num_views` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`num_ups` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`num_downs` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`image_square` VARCHAR(255) NULL DEFAULT NULL,
					`image_thumbnail` VARCHAR(255) NULL DEFAULT NULL,
					`image_small` VARCHAR(255) NULL DEFAULT NULL,
					`image_crop` VARCHAR(255) NULL DEFAULT NULL,
					`image_medium` VARCHAR(255) NULL DEFAULT NULL,
					`image_large` VARCHAR(255) NULL DEFAULT NULL,
					`image_original` VARCHAR(255) NULL DEFAULT NULL,
					`cover_title` VARCHAR(255) NULL DEFAULT NULL,
					`created_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`created_date` DATETIME NOT NULL,
					`updated_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`updated_date` DATETIME NULL DEFAULT NULL,
					`activated_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`activated_date` DATETIME NULL DEFAULT NULL,
					`publishing_date` DATETIME NULL DEFAULT NULL,
					`status` ENUM('draft','activated','not_activated','deleted') NOT NULL DEFAULT 'not_activated',
					`language` VARCHAR(10) NULL DEFAULT NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`article_id`),
					INDEX `idx_status` (`status`),
					INDEX `idx_category_id` (`category_id`),
					INDEX `idx_type` (`type`),
					INDEX `idx_language` (`language`),
					INDEX `idx_publishing_date` (`publishing_date`),
					INDEX `idx_slug` (`slug`),
					INDEX `idx_ordering` (`ordering`),
					INDEX `idx_archive` (`status`, `activated_date`, `category_id`),
					INDEX `idx_status_num_views` (`status`, `num_views`),
					INDEX `idx_status_num_comments` (`status`, `num_comments`),
					INDEX `idx_user` (`created_user`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###content_article_category_assoc`;",
				"CREATE TABLE `###content_article_category_assoc` (
					`article_id` INT(10) UNSIGNED NOT NULL,
					`category_id` INT(10) UNSIGNED NOT NULL,
					PRIMARY KEY (`article_id`, `category_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
				"DROP TABLE IF EXISTS `###content_article_folder_assoc`;",
				"CREATE TABLE `###content_article_folder_assoc` (
					`article_id` INT(10) UNSIGNED NOT NULL,
					`folder_id` INT(10) UNSIGNED NOT NULL,
					PRIMARY KEY (`article_id`, `folder_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###content_revision`;",
				"CREATE TABLE `###content_revision` (
					`revision_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`comment` TEXT NULL,
					`is_active` TINYINT(4) NOT NULL DEFAULT '0',
					`versioning_date` DATETIME NOT NULL,
					`article_id` INT(10) UNSIGNED NOT NULL,
					`category_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`categories` TEXT NULL,
					`tags` TEXT NULL,
					`type` ENUM('article','page','blog') NOT NULL DEFAULT 'article',
					`title` VARCHAR(255) NOT NULL,
					`sub_title` VARCHAR(255) NULL DEFAULT NULL,
					`slug` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`meta_description` TEXT NULL,
					`meta_keyword` TEXT NULL,
					`content` TEXT NOT NULL,
					`layout` TEXT NOT NULL,
					`author` VARCHAR(255) NOT NULL,
					`credit` VARCHAR(255) NULL DEFAULT NULL,
					`featured` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`image_icon` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`video_icon` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`num_views` INT(10) UNSIGNED NULL DEFAULT '0',
					`image_square` VARCHAR(255) NULL DEFAULT NULL,
					`image_thumbnail` VARCHAR(255) NULL DEFAULT NULL,
					`image_small` VARCHAR(255) NULL DEFAULT NULL,
					`image_crop` VARCHAR(255) NULL DEFAULT NULL,
					`image_medium` VARCHAR(255) NULL DEFAULT NULL,
					`image_large` VARCHAR(255) NULL DEFAULT NULL,
					`image_original` VARCHAR(255) NULL DEFAULT NULL,
					`cover_title` VARCHAR(255) NULL DEFAULT NULL,
					`created_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`created_date` DATETIME NOT NULL,
					`updated_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`updated_date` DATETIME NULL DEFAULT NULL,
					`activated_user` INT(11) UNSIGNED NULL DEFAULT NULL,
					`activated_date` DATETIME NULL DEFAULT NULL,
					`publishing_date` DATETIME NULL DEFAULT NULL,
					`status` ENUM('draft','activated','not_activated','deleted') NOT NULL DEFAULT 'not_activated',
					`language` VARCHAR(10) NULL DEFAULT NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`revision_id`),
					INDEX `idx_is_active` (`is_active`),
					INDEX `idx_article_id` (`article_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Content_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###content_article`;",
				"DROP TABLE IF EXISTS `###content_article_category_assoc`;",
				"DROP TABLE IF EXISTS `###content_article_folder_assoc`;",
				"DROP TABLE IF EXISTS `###content_revision`;",
			),
		),
		'callbacks' => array(
			'Content_Services_Installer::uninstallModule',
		),
	),
);
