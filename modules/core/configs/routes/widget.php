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
 * @version		2012-03-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing widgets
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Render widget
	'core_widget_render' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/widget/render',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'widget',
			'action'	 => 'render',
		),
	),

	////////// BACKEND ACTIONS //////////
	// Install widget
	'core_widget_install' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/widget/install',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'widget',
			'action'	 => 'install',
		),
	),
	
	// List widgets
	'core_widget_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/widget/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'widget',
			'action'	 => 'list',
		),
	),
	
	// Uninstall widget
	'core_widget_uninstall' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/widget/uninstall',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'widget',
			'action'	 => 'uninstall',
		),
	),
);
