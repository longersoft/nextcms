<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	configs
 * @since		1.0
 * @version		2012-05-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Ad module
 * 
 * @return array
 */
return array(
	'name'  => 'ad',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Advertising',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Manage banners',
	),
	'icon'		=> '/modules/ad/images/ad16.png',
	'thumbnail' => '/modules/ad/images/ad32.png',
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
		'ad_zone_list' => array(
			'icon'			 => '/modules/ad/images/zone16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.zone.list',
			'description'	 => 'Manage zones',
		),
		'ad_banner_list' => array(
			'icon'			 => '/modules/ad/images/ad16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.banner.list',
			'description'	 => 'Manage banners',
		),
		'ad_banner_place' => array(
			'icon'			 => '/modules/ad/images/layout16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.banner.place',
			'description'	 => 'Place banner on pages',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###ad_banner`;",
				"CREATE TABLE `###ad_banner` (
					`banner_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`title` VARCHAR(255) NOT NULL,
					`format` ENUM('image','flash','html','javascript') NOT NULL DEFAULT 'image',
					`code` TEXT NULL,
					`target` ENUM('_self','_blank') NULL DEFAULT '_self',
					`target_url` VARCHAR(255) NULL DEFAULT NULL,
					`url` VARCHAR(255) NULL DEFAULT NULL,
					`status` ENUM('activated','not_activated') NOT NULL DEFAULT 'not_activated',
					`created_date` DATETIME NOT NULL,
					`from_date` DATETIME NULL DEFAULT NULL,
					`to_date` DATETIME NULL DEFAULT NULL,
					PRIMARY KEY (`banner_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###ad_banner_page_assoc`;",
				"CREATE TABLE `###ad_banner_page_assoc` (
					`banner_id` INT(10) UNSIGNED NOT NULL,
					`zone_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`page_id` INT(10) UNSIGNED NULL DEFAULT NULL,
					`ordering` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`url` VARCHAR(255) NULL DEFAULT NULL,
					INDEX `idx_banner_id` (`banner_id`),
					INDEX `idx_zone_id` (`zone_id`),
					INDEX `idx_page_id` (`page_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###ad_zone`;",
				"CREATE TABLE `###ad_zone` (
					`zone_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(255) NOT NULL,
					`width` INT UNSIGNED NOT NULL DEFAULT '0',
					`height` INT UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (`zone_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Ad_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###ad_banner`;",
				"DROP TABLE IF EXISTS `###ad_banner_page_assoc`;",
				"DROP TABLE IF EXISTS `###ad_zone`;",
			),
		),
		'callbacks' => array(
			'Ad_Services_Installer::uninstallModule',
		),
	),
);
