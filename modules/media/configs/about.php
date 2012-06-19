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
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Media module
 * 
 * @return array
 */
return array(
	'name'  => 'media',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Media',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'This module is used to manage the photos',
	),
	'icon'		=> '/modules/media/images/media16.png',
	'thumbnail' => '/modules/media/images/media32.png',
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
		'dbAdapters'	=> 'mysql,pdo_mysql'
	),
	'backendMenu' => array(
		'media_photo_list' => array(
			'icon'			 => '/modules/media/images/photo16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.photo.list',
			'description'	 => 'Manage photos',
		),
		'media_flickr_import' => array(
			'icon'			 => '/modules/media/images/flickr16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.flickr.import',
			'description'	 => 'Import photos from Flickr',
		),
		'media_video_list' => array(
			'icon'			 => '/modules/media/images/video16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.video.list',
			'description'	 => 'Manage videos',
		),
		'media_config_config' => array(
			'icon'			 => '/modules/media/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###media_album`;",
				"CREATE TABLE `###media_album` (
					`album_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`slug` VARCHAR(255) NULL DEFAULT NULL,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NULL,
					`created_date` DATETIME NULL DEFAULT NULL,
					`user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
					`user_name` VARCHAR(100) NULL DEFAULT NULL,
					`num_views` INT(11) UNSIGNED NULL DEFAULT '0',
					`num_photos` INT(11) UNSIGNED NULL DEFAULT '0',
					`status` ENUM('activated','not_activated') NOT NULL DEFAULT 'not_activated',
					`activated_date` DATETIME NULL DEFAULT NULL,
					`cover` INT(11) UNSIGNED NULL DEFAULT NULL,
					`image_square` TEXT NULL,
					`image_thumbnail` TEXT NULL,
					`image_small` TEXT NULL,
					`image_crop` TEXT NULL,
					`image_medium` TEXT NULL,
					`image_large` TEXT NULL,
					`image_original` TEXT NULL,
					`language` VARCHAR(10) NULL DEFAULT NULL,
					PRIMARY KEY (`album_id`),
					INDEX `idx_status_activated_date` (`status`, `activated_date`),
					INDEX `idx_user` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###media_photo`;",
				"CREATE TABLE `###media_photo` (
					`photo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`flickr_id` VARCHAR(50) NULL DEFAULT NULL,
					`title` VARCHAR(255) NOT NULL,
					`slug` VARCHAR(255) NULL DEFAULT NULL,
					`description` TEXT NULL,
					`image_square` TEXT NULL,
					`image_thumbnail` TEXT NULL,
					`image_small` TEXT NULL,
					`image_crop` TEXT NULL,
					`image_medium` TEXT NULL,
					`image_large` TEXT NULL,
					`image_original` TEXT NULL,
					`num_comments` INT(10) UNSIGNED NULL DEFAULT '0',
					`num_views` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`num_downloads` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`num_ups` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`num_downs` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`uploaded_date` DATETIME NULL DEFAULT NULL,
					`user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
					`user_name` VARCHAR(100) NULL DEFAULT NULL,
					`photographer` VARCHAR(255) NULL DEFAULT NULL,
					`status` ENUM('activated','not_activated') NOT NULL DEFAULT 'not_activated',
					`activated_date` DATETIME NULL DEFAULT NULL,
					`language` VARCHAR(10) NULL DEFAULT NULL,
					PRIMARY KEY (`photo_id`),
					INDEX `idx_status_activated_date` (`status`, `activated_date`),
					INDEX `idx_status_num_views` (`status`, `num_views`),
					INDEX `idx_status_num_downloads` (`status`, `num_downloads`),
					INDEX `idx_flickr_id` (`flickr_id`),
					INDEX `idx_status_num_comments` (`status`, `num_comments`),
					INDEX `idx_user` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###media_photo_album_assoc`;",
				"CREATE TABLE `###media_photo_album_assoc` (
					`photo_id` INT(11) UNSIGNED NOT NULL,
					`album_id` INT(11) UNSIGNED NOT NULL,
					`ordering` INT(10) UNSIGNED NULL DEFAULT '0',
					PRIMARY KEY (`photo_id`, `album_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###media_playlist`;",
				"CREATE TABLE `###media_playlist` (
					`playlist_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`slug` VARCHAR(255) NULL DEFAULT NULL,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NULL,
					`created_date` DATETIME NULL DEFAULT NULL,
					`user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
					`user_name` VARCHAR(100) NULL DEFAULT NULL,
					`num_views` INT(11) UNSIGNED NULL DEFAULT '0',
					`num_videos` INT(11) UNSIGNED NULL DEFAULT '0',
					`status` ENUM('activated','not_activated') NOT NULL DEFAULT 'not_activated',
					`activated_date` DATETIME NULL DEFAULT NULL,
					`poster` INT(11) UNSIGNED NULL DEFAULT NULL,
					`image_square` TEXT NULL,
					`image_thumbnail` TEXT NULL,
					`image_small` TEXT NULL,
					`image_crop` TEXT NULL,
					`image_medium` TEXT NULL,
					`image_large` TEXT NULL,
					`image_original` TEXT NULL,
					`language` VARCHAR(10) NULL DEFAULT NULL,
					PRIMARY KEY (`playlist_id`),
					INDEX `idx_status_activated_date` (`status`, `activated_date`),
					INDEX `idx_user` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###media_video`;",
				"CREATE TABLE `###media_video` (
					`video_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`title` VARCHAR(255) NOT NULL,
					`slug` VARCHAR(255) NULL DEFAULT NULL,
					`description` TEXT NULL,
					`image_square` TEXT NULL,
					`image_thumbnail` TEXT NULL,
					`image_small` TEXT NULL,
					`image_crop` TEXT NULL,
					`image_medium` TEXT NULL,
					`image_large` TEXT NULL,
					`image_original` TEXT NULL,
					`num_comments` INT(10) UNSIGNED NULL DEFAULT '0',
					`num_views` INT(11) UNSIGNED NULL DEFAULT '0',
					`num_ups` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`num_downs` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`uploaded_date` DATETIME NULL DEFAULT NULL,
					`user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
					`user_name` VARCHAR(100) NULL DEFAULT NULL,
					`url` TEXT NULL,
					`embed_code` TEXT NULL,
					`duration` VARCHAR(10) NOT NULL DEFAULT '00:00:00',
					`credit` VARCHAR(255) NULL DEFAULT NULL,
					`status` ENUM('activated','not_activated') NOT NULL DEFAULT 'not_activated',
					`activated_date` DATETIME NULL DEFAULT NULL,
					`language` VARCHAR(10) NULL DEFAULT NULL,
					PRIMARY KEY (`video_id`),
					INDEX `idx_status_activated_date` (`status`, `activated_date`),
					INDEX `idx_status_num_views` (`status`, `num_views`),
					INDEX `idx_status_num_comments` (`status`, `num_comments`),
					INDEX `idx_user` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###media_video_playlist_assoc`;",
				"CREATE TABLE `###media_video_playlist_assoc` (
					`video_id` INT(11) UNSIGNED NOT NULL,
					`playlist_id` INT(11) UNSIGNED NOT NULL,
					`ordering` INT(10) UNSIGNED NULL DEFAULT '0',
					PRIMARY KEY (`video_id`, `playlist_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Media_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###media_album`;",
				"DROP TABLE IF EXISTS `###media_photo`;",
				"DROP TABLE IF EXISTS `###media_photo_album_assoc`;",
				"DROP TABLE IF EXISTS `###media_playlist`;",
				"DROP TABLE IF EXISTS `###media_video`;",
				"DROP TABLE IF EXISTS `###media_video_playlist_assoc`;",
			),
		),
		'callbacks' => array(
			'Media_Services_Installer::uninstallModule',
		),
	),
);
