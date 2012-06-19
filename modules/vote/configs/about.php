<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		vote
 * @subpackage	configs
 * @since		1.0
 * @version		2012-03-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Vote module
 * 
 * @return array
 */
return array(
	'name'  => 'vote',
	'title' => array(
		'translationKey' => '_about.title',
		'description'	 => 'Vote',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'	 => 'Allow users to vote the comments, articles, photos ...',
	),
	'icon'		=> '/modules/vote/images/vote16.png',
	'thumbnail'	=> '/modules/vote/images/vote32.png',
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
		'dbAdapters'	=> ''
	),
	'backendMenu' => array(
		'vote_config_config' => array(
			'icon'			 => '/modules/vote/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	'install'	  => array(
		'queries'	=> array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###vote`;",
				"CREATE TABLE `###vote` (
					`vote_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`entity_id` INT(10) UNSIGNED NOT NULL,
					`entity_class` VARCHAR(200) NOT NULL,
					`vote` TINYINT(1) NOT NULL DEFAULT '1',
					`user_id` INT(10) UNSIGNED NULL,
					`ip` VARCHAR(40) NULL,
					PRIMARY KEY (`vote_id`),
					INDEX `idx_entity` (`entity_id`, `entity_class`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(),
	),
	'uninstall' => array(
		'queries'	=> array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###vote`;",
			),
		),
		'callbacks' => array(),
	),
);
