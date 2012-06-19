<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	bootstrap
 * @since		1.0
 * @version		2012-05-22
 */

// Exit if the PHP version does not meet the requirement.
// NextCMS uses ZF that requires PHP 5.2.4 or later.
// See http://framework.zend.com/manual/en/requirements.introduction.html
if (version_compare(phpversion(), '5.2.4', '<') === true) {
    die('ERROR: Your PHP version is ' . phpversion() . '. The app requires PHP 5.2.4 or newer.');
}

define('DS', 		   DIRECTORY_SEPARATOR);
define('PS', 		   PATH_SEPARATOR);

define('APP_ROOT_DIR', dirname(__FILE__));
define('APP_TEMP_DIR', APP_ROOT_DIR . DS . 'temp');

// Version of Dojo and jQuery libraries
define('APP_DOJO_VER',   '1.6.0');
define('APP_JQUERY_VER', '1.7.1');

// This variable is used to prevent user from accessing the script directly.
// At the top of every PHP files (but this file, ofcourse), you have to add the
// following line to check if the request is valid:
// 		defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');
define('APP_VALID_REQUEST', true);

// Define application environment. 
// User can set the value in .htacess file located at the root directory.
// The common values are:
// - dev: Indicates that we are in development environment
// - pro: Indicates the production environment
// The app will look for the configuration file located as follow:
//		APP_ROOT_DIR . '/configs/application.' . strtolower(APP_ENV) . '.php'
define('APP_ENV', getenv('APP_ENV') ? getenv('APP_ENV') : 'pro');

// Set the level of error reporting based on the environment
switch (APP_ENV) {
	case 'dev':
	case 'test':
		error_reporting(E_ALL);
		break;
	case 'pro':
	default:
		error_reporting(0);
		break;
}

// Set the include path, to ensure that the app can find the request file
// from library directory
define('APP_LIB_DIR', APP_ROOT_DIR . DS . 'libraries');
set_include_path(PS . APP_LIB_DIR . PS . get_include_path());

// Define the configuration file
$hostName = $_SERVER['SERVER_NAME'];
$hostName = (substr($hostName, 0, 3) == 'www') ? substr($hostName, 4) : $hostName;
$default  = APP_ROOT_DIR . DS . 'configs' . DS . 'application.' . strtolower(APP_ENV) . '.php';
$host     = APP_ROOT_DIR . DS . 'configs' . DS . $hostName . '.' . strtolower(APP_ENV) . '.php';
define('APP_HOST_CONFIG', file_exists($host) ? $hostName : 'application');

// Create new app instance
require_once 'Zend/Application.php';
$application = new Zend_Application(
	APP_ENV,
	file_exists($host) ? $host : $default
);

$options = array(
	'bootstrap' => array(
		'path'	=> APP_ROOT_DIR . DS . 'Bootstrap.php',
		'class' => 'Bootstrap',
	),
	'resources' => array(
		'Core_Application_Resource_Modules' => array(),
		'frontController' => array(
			'controllerDirectory' => APP_ROOT_DIR . DS . 'controllers',
			'moduleDirectory'	  => APP_ROOT_DIR . DS . 'modules',
		),
	),
);

// Run it!
$options = $application->mergeOptions($application->getOptions(), $options);
$application->setOptions($options)
			->bootstrap()
			->run();
