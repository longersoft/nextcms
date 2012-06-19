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
 * @version		2011-12-31
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing cron tasks
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Configure task
	'core_task_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/task/config',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'task',
			'action'	 => 'config',
		),
	),
	
	// Install task
	'core_task_install' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/task/install',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'task',
			'action'	 => 'install',
		),
	),
	
	// List tasks
	'core_task_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/task/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'task',
			'action'	 => 'list',
		),
	),
	
	// Run task
	'core_task_run' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/task/run',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'task',
			'action'	 => 'run',
		),
	),
	
	// Schedule task
	'core_task_schedule' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/task/schedule',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'task',
			'action'	 => 'schedule',
		),
	),
	
	// Uninstall task
	'core_task_uninstall' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/task/uninstall',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'task',
			'action'	 => 'uninstall',
		),
	),
);
