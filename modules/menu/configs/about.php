<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Menu module
 * 
 * @return array
 */
return array(
	'name'  => 'menu',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Menu',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Build and manage menus',
	),
	'icon'		=> '/modules/menu/images/menu16.png',
	'thumbnail'	=> '/modules/menu/images/menu32.png',
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
		'menu_menu_list' => array(
			'icon'			 => '/modules/menu/images/menu16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.menu.list',
			'description'	 => 'Manage menus',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###menu`;",
				"CREATE TABLE `###menu` (
					`menu_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NULL,
					`created_user` INT(11) UNSIGNED NOT NULL,
					`created_date` DATETIME NULL DEFAULT NULL,
					`language` VARCHAR(10) NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`menu_id`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###menu_item`;",
				"CREATE TABLE `###menu_item` (
					`item_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`menu_id` INT(11) UNSIGNED NOT NULL,
					`title` VARCHAR(200) NOT NULL,
					`sub_title` VARCHAR(200) NULL DEFAULT NULL,
					`description` TEXT NULL,
					`link` TEXT NOT NULL,
					`target` ENUM('_self','_blank') NULL DEFAULT '_self',
					`image` VARCHAR(255) NULL DEFAULT NULL,
					`left_id` INT(11) UNSIGNED NOT NULL,
					`right_id` INT(11) UNSIGNED NOT NULL,
					`parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`html_id` VARCHAR(100) NULL DEFAULT NULL,
					`css_class` VARCHAR(255) NULL DEFAULT NULL,
					`css_style` TEXT NULL DEFAULT NULL,
					PRIMARY KEY (`item_id`),
					INDEX `idx_menu_id` (`menu_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Menu_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###menu`;",
				"DROP TABLE IF EXISTS `###menu_item`;",
			),
		),
		'callbacks' => array(
			'Menu_Services_Installer::uninstallModule',
		),
	),
);
