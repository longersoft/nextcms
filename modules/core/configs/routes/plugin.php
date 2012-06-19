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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing plugins
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Configure plugin
	'core_plugin_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/plugin/config',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'plugin',
			'action'	 => 'config',
		),
	),
	
	// Disable plugin
	'core_plugin_disable' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/plugin/disable',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'plugin',
			'action'	 => 'disable',
		),
	),
	
	// Enable plugin
	'core_plugin_enable' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/plugin/enable',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'plugin',
			'action'	 => 'enable',
		),
	),
	
	// Install plugin
	'core_plugin_install' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/plugin/install',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'plugin',
			'action'	 => 'install',
		),
	),
	
	// List plugins
	'core_plugin_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/plugin/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'plugin',
			'action'	 => 'list',
		),
	),
	
	// Uninstall plugin
	'core_plugin_uninstall' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/plugin/uninstall',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'plugin',
			'action'	 => 'uninstall',
		),
	),
);
