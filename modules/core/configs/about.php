<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	configs
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the Core module.
 * This is standard file of a module information file.
 * 
 * @return array
 */
return array(
	// The name of module. It has to be in the lowercase and 
	// is exactly the name of the directory containing the module
	'name'  => 'core',
	'title' => array(
		'translationKey' => '_about.title',
		'description' 	=> 'Core',
	),
	'description' => array(
		'translationKey' => '_about.description',
		'description'    => 'Provides most important tasks such as managing users, permissions, configurations, etc.',
	),
	'icon'		=> '/modules/core/images/core16.png',
	'thumbnail' => '/modules/core/images/core32.png',
	'website'   => null,
	'author'    => 'Nguyen Huu Phuoc',
	'email' 	=> 'thenextcms@gmail.com',
	'version' 	 => '1.0',				// The module version
	'appVersion' => '1.0+',				// The app version that the module is compatible with.
										// You can use + or - after the version string to let users know that
										// the module can run with a newer or lower version of the app.
	'license'	 => 'http://nextcms.org/license.txt',	// Information about the license
	'requirements' => array(
		'modules'       => '',			// List of modules, separated by a comma, which have to be installed before installing the module
		'phpExtensions' => '',			// List of required PHP extensions, separated by a comma
		'php'			=> '5.2.4+',	// PHP version
		'dbAdapters'	=> 'mysql,pdo_mysql'			// List of databases adapters that are supported, separated by a comma.
														// The app allows or does not allow to install the module from the back-end
														// if the current database adapter is not one of supported adapters. 
	),
	// The menu items that will be shown at the top menu in the back-end.
	// The standard format is:
	//		'backendMenu' => array(
	//			'nameOfRoute' => array(				// The name of route which defines the controller action. 
	//												// The standard format is modulename_controllername_actionname (all in lowercase)
	//				'translationKey' => '...',		// The value of this language key item will be used as label of menu item
	//				'description' 	 => '...' 		// The default label of menu item if the app cannot find the language item
	//				'ajax' => true|false,			// The controller action will be loaded using an Ajax request. 
	//												// The default value is true
	//			) 
	//		)
	'backendMenu' => array(
		'core_module_list' => array(
			'icon'			 => '/modules/core/images/module16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.module.list',
			'description'	 => 'Manage extensions',
		),
		'core_task_list' => array(
			'icon'			 => '/modules/core/images/task16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.task.list',
			'description'	 => 'Manage cron tasks',
		),
		'core_privilege_list' => array(
			'icon'			 => '/modules/core/images/privilege16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.privilege.list',
			'description'	 => 'Manage privileges',
		),
		'core_user_list' => array(
			'icon'			 => '/modules/core/images/user16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.user.list',
			'description'	 => 'Manage users',
		),
		'core_template_list' => array(
			'icon'			 => '/modules/core/images/template16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.template.list',
			'description'	 => 'Manage templates',
		),
		'core_page_list' => array(
			'icon'			 => '/modules/core/images/page16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.page.list',
			'description'	 => 'Manage pages',
		),
		'core_language_list' => array(
			'icon'			 => '/modules/core/images/language16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.language.list',
			'description'	 => 'Manage language files',
		),
		'core_error_list' => array(
			'icon'			 => '/modules/core/images/error16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.error.list',
			'description'	 => 'Manage errors',
		),
		'core_accesslog_list' => array(
			'icon'			 => '/modules/core/images/accesslog16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.accesslog.list',
			'description'	 => 'Manage access logs',
		),
		'core_permalink_config' => array(
			'icon'			 => '/modules/core/images/permalink16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.permalink.config',
			'description'	 => 'Configure permalinks',
		),
		'core_config_config' => array(
			'icon'			 => '/modules/core/images/config16.png',
			'ajax'			 => true,
			'translationKey' => '_menu.config.config',
			'description'	 => 'Configure module',
		),
	),
	// The queries that will be executed to create the database tables for the module.
	// It can supports multiple databases. The valid format is as follow:
	//		'install' => array(
	//			'queries' => array(
	//				'dbAdapters' => array(
	//					"query1", "query2", ....
	//				),
	//			),
	//			'callback' => array(
	//				'ClassName::classMethod',
	//			),
	//		),
	// where:
	//		- dbAdapters: List of database adapters that uses the same database schema (but might be different PHP drivers), 
	//		separated by a comma
	//		- query1, query2, ... are the SQL queries. Because the app supports database prefix, 
	//		so ensure that you add ### at the begining of table name. These characters will be replaced with
	//		the database prefix when installing app.
	'install' => array(
		'queries' => array(
			// For mysql and pdo_mysql adapter
			'mysql,pdo_mysql' => array(
				"DROP TABLE IF EXISTS `###core_access_log`;",
				"CREATE TABLE `###core_access_log` (
					`log_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`user_id` INT(10) UNSIGNED NULL DEFAULT '0',
					`title` VARCHAR(255) NULL DEFAULT NULL,
					`url` TEXT NOT NULL,
					`module` VARCHAR(50) NULL DEFAULT NULL,
					`ip` VARCHAR(40) NOT NULL,
					`accessed_date` DATETIME NOT NULL,
					`params` TEXT NULL,
					PRIMARY KEY (`log_id`),
					INDEX `idx_module` (`module`),
					INDEX `idx_accessed_date` (`accessed_date`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_config`;",
				"CREATE TABLE `###core_config` (
					`module` VARCHAR(50) NOT NULL, 
					`config_key` VARCHAR(100) NOT NULL, 
					`config_value` TEXT NULL, 
					UNIQUE INDEX `idx_module_key` (`module`, `config_key`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_error`;",
				"CREATE TABLE `###core_error` (
					`error_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`created_user` INT(10) UNSIGNED NULL DEFAULT NULL,
					`created_date` DATETIME NOT NULL,
					`uri` TEXT NOT NULL,
					`module` VARCHAR(255) NOT NULL,
					`controller` VARCHAR(255) NOT NULL,
					`action` VARCHAR(255) NOT NULL,
					`class` VARCHAR(255) NOT NULL,
					`file` VARCHAR(255) NOT NULL,
					`line` INT(11) UNSIGNED NOT NULL,
					`message` TEXT NOT NULL,
					`trace` TEXT NOT NULL,
					PRIMARY KEY (`error_id`),
					INDEX `idx_module` (`module`),
					INDEX `idx_created_date` (`created_date`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_dashboard`;",
				"CREATE TABLE `###core_dashboard` (
					`dashboard_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`user_id` INT(11) UNSIGNED NOT NULL,
					`layout` TEXT NOT NULL,
					PRIMARY KEY (`dashboard_id`),
					INDEX `idx_user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_hook`;",
				"CREATE TABLE `###core_hook` (
					`hook_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`ordering` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`module` VARCHAR(100) NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`filter` TINYINT UNSIGNED NOT NULL DEFAULT '0',
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`thumbnail` TEXT NULL,
					`website` VARCHAR(255) NULL DEFAULT NULL,
					`author` VARCHAR(255) NULL DEFAULT NULL,
					`email` VARCHAR(100) NULL DEFAULT NULL,
					`version` VARCHAR(20) NULL DEFAULT NULL,
					`app_version` VARCHAR(20) NULL DEFAULT NULL,
					`license` TEXT NULL,
					`options` TEXT NULL,
					PRIMARY KEY (`hook_id`),
					INDEX `idx_module` (`module`),
					INDEX `idx_name` (`name`),
					INDEX `idx_filter` (`filter`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_hook_target`;",
				"CREATE TABLE `###core_hook_target` (
					`target_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`target_module` VARCHAR(100) NOT NULL,
					`target_name` VARCHAR(255) NOT NULL,
					`hook_module` VARCHAR(100) NOT NULL,
					`hook_name` VARCHAR(100) NOT NULL,
					`hook_method` VARCHAR(100) NOT NULL,
					`params` TEXT NULL,
					`echo` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (`target_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_mail_queue`;",
				"CREATE TABLE `###core_mail_queue` (
					`mail_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					`from_name` VARCHAR(100) NULL DEFAULT NULL,
					`from_email` VARCHAR(255) NOT NULL,
					`to_name` VARCHAR(100) NULL DEFAULT NULL,
					`to_email` VARCHAR(255) NOT NULL,
					`subject` VARCHAR(255) NOT NULL,
					`content` TEXT NOT NULL,
					`num_attempts` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`success` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
					`last_attempt` DATETIME NULL DEFAULT NULL,
					`queued_date` DATETIME NOT NULL,
					`sent_date` DATETIME NULL DEFAULT NULL,
					PRIMARY KEY (`mail_id`),
					INDEX `idx_success` (`success`),
					INDEX `idx_num_attempts` (`num_attempts`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_mail_sent`;",
				"CREATE TABLE `###core_mail_sent` (
					`mail_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					`from_name` VARCHAR(100) NULL DEFAULT NULL,
					`from_email` VARCHAR(255) NOT NULL,
					`to_name` VARCHAR(100) NULL DEFAULT NULL,
					`to_email` VARCHAR(255) NOT NULL,
					`subject` VARCHAR(255) NOT NULL,
					`content` TEXT NOT NULL,
					`num_attempts` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`success` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
					`last_attempt` DATETIME NULL DEFAULT NULL,
					`queued_date` DATETIME NOT NULL,
					`sent_date` DATETIME NULL DEFAULT NULL,
					PRIMARY KEY (`mail_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_module`;",
				"CREATE TABLE `###core_module` (
					`module_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(100) NOT NULL,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`thumbnail` TEXT NULL DEFAULT NULL,
					`website` VARCHAR(255) NULL DEFAULT NULL,
					`author` VARCHAR(255) NULL DEFAULT NULL,
					`email` VARCHAR(100) NULL DEFAULT NULL,
					`version` VARCHAR(20) NULL DEFAULT NULL,
					`app_version` VARCHAR(20) NULL DEFAULT NULL,
					`required_modules` VARCHAR(200) NULL DEFAULT NULL,
					`license` TEXT NULL,
					PRIMARY KEY (`module_id`),
					UNIQUE INDEX `idx_name` (`name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_openid_assoc`;",
				"CREATE TABLE `###core_openid_assoc` (
					`url` VARCHAR(255) NOT NULL,
					`handle` VARCHAR(255) NOT NULL,
					`mac_func` CHAR(16) NOT NULL,
					`secret` VARCHAR(255) NOT NULL,
					`expires` INT(11) NOT NULL,
					PRIMARY KEY (`url`),
					INDEX `idx_expires` (`expires`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_openid_discovery`;",
				"CREATE TABLE `###core_openid_discovery` (
					`discovery_id` VARCHAR(255) NOT NULL,
					`real_id` VARCHAR(255) NOT NULL,
					`server` VARCHAR(255) NOT NULL,
					`version` FLOAT NULL DEFAULT NULL,
					`expires` INT(11) NOT NULL,
					PRIMARY KEY (`discovery_id`),
					INDEX `idx_expires` (`expires`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_openid_nonce`;",
				"CREATE TABLE `###core_openid_nonce` (
					`nonce` VARCHAR(255) NOT NULL,
					`created` INT(11) NOT NULL,
					PRIMARY KEY (`nonce`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_openid_user_assoc`;",
				"CREATE TABLE `###core_openid_user_assoc` (
					`user_id` INT(10) UNSIGNED NOT NULL,
					`openid_url` VARCHAR(255) NOT NULL,
					INDEX `idx_user_id` (`user_id`),
					INDEX `idx_openid_url` (`openid_url`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_page`;",
				"CREATE TABLE `###core_page` (
					`page_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(200) NOT NULL,
					`title` VARCHAR(255) NULL DEFAULT NULL,
					`route` VARCHAR(200) NOT NULL,
					`url` TEXT NULL,
					`ordering` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`template` VARCHAR(200) NOT NULL DEFAULT 'default',
					`layout` TEXT NULL,
					`cache_lifetime` INT(10) UNSIGNED NULL DEFAULT '0',
					`language` VARCHAR(10) NOT NULL,
					`translations` TEXT NULL,
					PRIMARY KEY (`page_id`),
					INDEX `idx_template` (`template`),
					INDEX `idx_language` (`language`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_plugin`;",
				"CREATE TABLE `###core_plugin` (
					`plugin_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`ordering` INT(10) UNSIGNED NOT NULL DEFAULT '0',
					`module` VARCHAR(100) NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`enabled` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`thumbnail` TEXT NULL,
					`website` VARCHAR(255) NULL DEFAULT NULL,
					`author` VARCHAR(255) NULL DEFAULT NULL,
					`email` VARCHAR(100) NULL DEFAULT NULL,
					`version` VARCHAR(20) NULL DEFAULT NULL,
					`app_version` VARCHAR(20) NULL DEFAULT NULL,
					`license` TEXT NULL,
					`options` TEXT NULL,
					PRIMARY KEY (`plugin_id`),
					INDEX `idx_module` (`module`),
					INDEX `idx_name` (`name`),
					INDEX `idx_enabled` (`enabled`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_privilege`;",
				"CREATE TABLE `###core_privilege` (
					`privilege_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`description` TEXT NULL,
					`module_name` VARCHAR(100) NOT NULL,
					`controller_name` VARCHAR(100) NOT NULL,
					`action_name` VARCHAR(100) NOT NULL,
					`extension_type` ENUM('module','hook','plugin','task','widget') NOT NULL DEFAULT 'module',
					PRIMARY KEY (`privilege_id`),
					INDEX `idx_module_controller_extension` (`module_name`, `controller_name`, `extension_type`),
					INDEX `idx_module_name` (`module_name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_resource`;",
				"CREATE TABLE `###core_resource` (
					`resource_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`parent_id` VARCHAR(50) NULL DEFAULT NULL,
					`description` TEXT NULL,
					`module_name` VARCHAR(255) NOT NULL,
					`controller_name` VARCHAR(255) NOT NULL,
					`extension_type` ENUM('module','hook','plugin','task','widget') NOT NULL DEFAULT 'module',
					PRIMARY KEY (`resource_id`),
					INDEX `idx_module_controller_extension` (`module_name`, `controller_name`, `extension_type`),
					INDEX `idx_module_name` (`module_name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_role`;",
				"CREATE TABLE `###core_role` (
					`role_id` INT(50) UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(200) NOT NULL,
					`num_users` INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`description` VARCHAR(255) NOT NULL,
					`locked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (`role_id`),
					UNIQUE INDEX `idx_name` (`name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_rule`;",
				"CREATE TABLE `###core_rule` (
					`rule_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`obj_id` INT(50) UNSIGNED NOT NULL,
					`obj_type` ENUM('user','role') NOT NULL DEFAULT 'role',
					`allow` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
					`resource_name` VARCHAR(100) NULL DEFAULT NULL,
					`module_name` VARCHAR(255) NULL DEFAULT NULL,
					`controller_name` VARCHAR(255) NULL DEFAULT NULL,
					`action_name` VARCHAR(50) NULL DEFAULT NULL,
					`extension_type` VARCHAR(20) NULL DEFAULT 'module',
					PRIMARY KEY (`rule_id`),
					INDEX `idx_module_controller_extension` (`module_name`, `controller_name`, `extension_type`),
					INDEX `idx_module_name` (`module_name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_session`;",
				"CREATE TABLE `###core_session` (
					`session_id` VARCHAR(255) NOT NULL,
					`data` TEXT NOT NULL,
					`modified` INT(11) UNSIGNED NULL DEFAULT NULL,
					`lifetime` INT(11) UNSIGNED NULL DEFAULT NULL,
					PRIMARY KEY (`session_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_task`;",
				"CREATE TABLE `###core_task` (
					`task_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`module` VARCHAR(100) NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`thumbnail` TEXT NULL,
					`website` VARCHAR(255) NULL DEFAULT NULL,
					`author` VARCHAR(255) NULL DEFAULT NULL,
					`email` VARCHAR(100) NULL DEFAULT NULL,
					`version` VARCHAR(20) NULL DEFAULT NULL,
					`app_version` VARCHAR(20) NULL DEFAULT NULL,
					`license` TEXT NULL,
					`last_run` INT(10) UNSIGNED NULL DEFAULT NULL,
					`next_run` INT(10) UNSIGNED NULL DEFAULT NULL,
					`time_mask` VARCHAR(50) NOT NULL,
					`options` TEXT NULL,
					PRIMARY KEY (`task_id`),
					INDEX `idx_module` (`module`),
					INDEX `idx_name` (`name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_user`;",
				"CREATE TABLE `###core_user` (
					`user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`role_id` INT(11) UNSIGNED NOT NULL,
					`user_name` VARCHAR(100) NOT NULL,
					`email` VARCHAR(255) NOT NULL,
					`password` VARCHAR(50) NOT NULL,
					`status` ENUM('activated','not_activated','deleted','banned') NOT NULL DEFAULT 'not_activated',
					`activation_key` VARCHAR(32) NULL DEFAULT NULL,
					`created_date` DATETIME NULL DEFAULT NULL,
					`logged_date` DATETIME NULL DEFAULT NULL,
					`is_online` TINYINT(1) UNSIGNED NULL DEFAULT '0',
					`full_name` VARCHAR(100) NULL DEFAULT NULL,
					`avatar` VARCHAR(255) NULL DEFAULT NULL,
					`dob` DATE NULL DEFAULT NULL,
					`gender` ENUM('m','f') NULL DEFAULT NULL,
					`website` VARCHAR(255) NULL DEFAULT NULL,
					`bio` TEXT NULL,
					`signature` TEXT NULL,
					`twitter` VARCHAR(200) NULL DEFAULT NULL,
					`facebook` VARCHAR(200) NULL DEFAULT NULL,
					`flickr` VARCHAR(200) NULL DEFAULT NULL,
					`youtube` VARCHAR(200) NULL DEFAULT NULL,
					`linkedin` VARCHAR(200) NULL DEFAULT NULL,
					`country` VARCHAR(200) NULL DEFAULT NULL,
					`language` VARCHAR(200) NULL DEFAULT NULL,
					`timezone` VARCHAR(200) NULL DEFAULT NULL,
					PRIMARY KEY (`user_id`),
					UNIQUE INDEX `idx_user_name` (`user_name`),
					UNIQUE INDEX `idx_email` (`email`),
					INDEX `idx_activation_key` (`activation_key`),
					INDEX `idx_role_id` (`role_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		
				"DROP TABLE IF EXISTS `###core_widget`;",
				"CREATE TABLE `###core_widget` (
					`widget_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`module` VARCHAR(100) NOT NULL,
					`name` VARCHAR(100) NOT NULL,
					`title` VARCHAR(255) NOT NULL,
					`description` TEXT NOT NULL,
					`thumbnail` TEXT NULL,
					`website` VARCHAR(255) NULL DEFAULT NULL,
					`author` VARCHAR(255) NULL DEFAULT NULL,
					`email` VARCHAR(100) NULL DEFAULT NULL,
					`version` VARCHAR(20) NULL DEFAULT NULL,
					`app_version` VARCHAR(20) NULL DEFAULT NULL,
					`license` TEXT NULL,
					PRIMARY KEY (`widget_id`),
					INDEX `idx_module` (`module`),
					INDEX `idx_name` (`name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			),
		),
		'callbacks' => array(
			'Core_Services_Installer::installModule',
		),
	),
	// The format is the same as install. The app will execute these queries when you uninstall the module.
	// Because you are seeing the root module, there is no uninstall query here.
	'uninstall' => array(
		'queries'	=> array(),
		'callbacks' => array(),
	),
);
