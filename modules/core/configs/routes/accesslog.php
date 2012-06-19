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
 * Define routes for managing access logs
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Delete access log
	'core_accesslog_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/accesslog/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'accesslog',
			'action'	 => 'delete',
		),
	),	

	// List access logs
	'core_accesslog_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/accesslog/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'accesslog',
			'action'	 => 'list',
		),
	),
	
	// View the access log details
	'core_accesslog_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/accesslog/view',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'accesslog',
			'action'	 => 'view',
		),
	),
);
