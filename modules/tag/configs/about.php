<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	configs
 * @since		1.0
 * @version		2012-05-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Tag module
 * 
 * @return array
 */
return array(
	'name'  => 'tag',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Tag',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Tagging the content',
	),
	'icon'		=> '/modules/tag/images/tag16.png',
	'thumbnail' => '/modules/tag/images/tag32.png',
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
		'tag_tag_list' => array(
			'icon'			 => '/modules/tag/images/tag16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.tag.list',
			'description'	 => 'Manage tags',
		),
		'tag_config_config' => array(
			'icon'			 => '/modules/tag/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	'install' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###tag`;",
				"CREATE TABLE `###tag` (
					`tag_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`language` VARCHAR(10) NOT NULL DEFAULT 'en_US',
					`title` VARCHAR(255) NOT NULL,
					`slug` VARCHAR(255) NOT NULL,
					PRIMARY KEY (`tag_id`),
					UNIQUE INDEX `idx_language_slug` (`language`, `slug`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###tag_entity_assoc`;",
				"CREATE TABLE `###tag_entity_assoc` (
					`tag_id` INT(10) UNSIGNED NOT NULL,
					`entity_id` INT(10) UNSIGNED NOT NULL,
					`entity_class` VARCHAR(255) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Tag_Services_Installer::installModule',
		),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###tag`;",
				"DROP TABLE IF EXISTS `###tag_entity_assoc`;",
			),
		),
		'callbacks' => array(
			'Tag_Services_Installer::uninstallModule',
		),
	),
);
