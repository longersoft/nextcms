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
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Feedimporter task
 * 
 * @return array
 */
return array(
	'name'  => 'feedimporter',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Feed Importer',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Import articles from RSS/Atom channels automatically',
	),
	'thumbnail' => '/modules/content/tasks/feedimporter/feedimporter32.png',
	'website'   => null,
	'author'    => 'Nguyen Huu Phuoc',
	'email' 	=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'requirements' => array(
		'phpExtensions' => '',
		'php'			=> '5.2.4+',
		'dbAdapters'	=> 'mysql,pdo_mysql',
	),
	'timeMask' => '0 0 * * *',
	'options'  => array(),
	'install'  => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###content_feed_entry`;",
				"CREATE TABLE `###content_feed_entry` (
					`entry_id` INT UNSIGNED NULL AUTO_INCREMENT,
					`feed_url` VARCHAR(255) NOT NULL,
					`link` VARCHAR(1000) NOT NULL,
					`article_id` INT UNSIGNED NOT NULL,
					`created_date` DATETIME NOT NULL,
					INDEX `idx_link` (`link`),
					PRIMARY KEY (`entry_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(),
	),
	'uninstall' => array(
		'queries' => array(
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###content_feed_entry`;",
			),
		),
		'callbacks' => array(),
	),
);
