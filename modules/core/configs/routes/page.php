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
 * @version		2012-02-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing pages
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new page
	'core_page_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/add',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'add',
		),
	),

	// Delete page
	'core_page_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'delete',
		),
	),
	
	// Edit page
	'core_page_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/edit',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'edit',
		),
	),
	
	// Export layout from XML file
	'core_page_export' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/export',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'export',
		),
	),
	
	// Set filters to widget
	'core_page_filter' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/filter',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'filter',
			'allowed'	 => true,
		),
	),
	
	// Import layout from XML file
	'core_page_import' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/import',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'import',
		),
	),
	
	// Layout page
	'core_page_layout' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/layout',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'layout',
		),
	),

	// List pages
	'core_page_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'list',
		),
	),
	
	// Order the pages
	'core_page_order' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/order',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'order',
		),
	),
	
	// Set control's properties
	'core_page_property' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/page/property',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'page',
			'action'	 => 'property',
			'allowed'	 => true,
		),
	),
);
