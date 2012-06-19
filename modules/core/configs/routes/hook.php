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

/**
 * Define routes for managing hooks
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Configure hook
	'core_hook_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/hook/config',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'hook',
			'action'	 => 'config',
		),
	),
	
	// Install hook
	'core_hook_install' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/hook/install',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'hook',
			'action'	 => 'install',
		),
	),
	
	// List hooks
	'core_hook_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/hook/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'hook',
			'action'	 => 'list',
		),
	),
	
	// Uninstall hook
	'core_hook_uninstall' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/hook/uninstall',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'hook',
			'action'	 => 'uninstall',
		),
	),
);
