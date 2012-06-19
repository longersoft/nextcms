<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	configs
 * @since		1.0
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Category module
 * 
 * @return array
 */
return array(
	'name'  => 'category',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Category',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Manage categories',
	),
	'icon'		=> '/modules/category/images/category16.png',
	'thumbnail' => '/modules/category/images/category32.png',
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
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###category`;",
				"CREATE TABLE `###category` (
					`category_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`parent_id` INT(10) UNSIGNED NULL DEFAULT '0',
					`left_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`right_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`module` VARCHAR(50) NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`slug` VARCHAR(255) NULL DEFAULT NULL,
					`image` VARCHAR(255) NULL DEFAULT NULL,
					`meta_description` TEXT NULL,
					`meta_keyword` TEXT NULL,
					`language` VARCHAR(10) NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`category_id`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
				"DROP TABLE IF EXISTS `###category_folder`;",
				"CREATE TABLE `###category_folder` (
					`folder_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`entity_class` VARCHAR(200) NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`language` VARCHAR(10) NULL DEFAULT NULL,
					PRIMARY KEY (`folder_id`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###category`;",
				"DROP TABLE IF EXISTS `###category_folder`;",
			),
		),
		'callbacks' => array(),
	),
);
