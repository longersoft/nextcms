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
 * Define routes for managing errors
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Delete error
	'core_error_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/error/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'error',
			'action'	 => 'delete',
		),
	),
	
	// List errors
	'core_error_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/error/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'error',
			'action'	 => 'list',
		),
	),
	
	// View the error details
	'core_error_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/error/view',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'error',
			'action'	 => 'view',
		),
	),
);
