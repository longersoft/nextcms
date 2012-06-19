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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

// You can declare the routes as follow:
//		$routes = array();
//		$routes['core_auth_login']['type']  = 'Zend_Controller_Router_Route_Static';
//		$routes['core_auth_login']['route'] = 'login';
//		$routes['core_auth_login']['defaults']['module']	 = 'core';
//		$routes['core_auth_login']['defaults']['controller'] = 'auth';
//		$routes['core_auth_login']['defaults']['action']	 = 'login';
//		return $routes;	

/**
 * Define routes for authentications
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Deny to access
	'core_auth_deny' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/auth/deny',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'auth',
			'action'	 => 'deny',
		),
	),	

	// Login
	'core_auth_login' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'login',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'auth',
			'action'	 => 'login',
		),
	),
	
	////////// BACKEND ACTIONS //////////
	// Logout
	'core_auth_logout' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'logout',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'auth',
			'action'	 => 'logout',
			'allowed'	 => true,
			'csrf'		 => array(
				'enabled' => true,
				'request' => 'get',
				'retrive' => 'get',
			),
			'track' => array(
				'enabled' => false,
			),
		),
	),
);
