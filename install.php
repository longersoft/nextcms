<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	install
 * @since		1.0
 * @version		2012-03-03
 */

// NextCMS uses ZF that requires PHP 5.2.4 or later.
// See http://framework.zend.com/manual/en/requirements.introduction.html
if (version_compare(phpversion(), '5.2.4', '<') === true) {
	die('ERROR: Your PHP version is ' . phpversion() . '. The app requires PHP 5.2.4 or newer.');
}

error_reporting(E_ALL);

define('DS',				DIRECTORY_SEPARATOR);
define('PS', 				PATH_SEPARATOR);

// Version of Dojo and jQuery libraries
define('APP_DOJO_VER',      '1.6.0');
define('APP_JQUERY_VER',    '1.7.1');

define('APP_VALID_REQUEST', true);
define('APP_ENV', 			getenv('APP_ENV') ? getenv('APP_ENV') : 'pro');
define('APP_ROOT_DIR', 		dirname(__FILE__));
define('APP_LIB_DIR', 		APP_ROOT_DIR . DS . 'libraries');
set_include_path(PS . APP_LIB_DIR . PS . get_include_path());

// Create new installer
require_once 'Zend/Application.php';
$application = new Zend_Application(
	APP_ENV,
	array(
		'phpsettings' => array(
			'display_startup_errors' => 1,
			'display_errors' 		 => 1,
		),
		'bootstrap' => array(
			'path' 	=> APP_ROOT_DIR . DS . 'Installer.php',
			'class' => 'Installer',
		),
		'resources' => array(
			'frontController' => array(
				'controllerDirectory' => APP_ROOT_DIR . DS . 'controllers',
				'moduleDirectory'     => APP_ROOT_DIR . DS . 'modules',
			),
		),
	)
);

// Run it!
$application->bootstrap()->run();
