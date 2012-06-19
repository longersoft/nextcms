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
 * Define routes for managing user's role
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new role
	'core_role_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/role/add',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'role',
			'action'	 => 'add',
		),
	),
	
	// Delete role
	'core_role_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/role/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'role',
			'action'	 => 'delete',
		),
	),
	
	// List roles
	'core_role_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/role/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'role',
			'action'	 => 'list',
		),
	),
	
	// Lock role. The locked role cannot be updated or set the permissions
	'core_role_lock' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/role/lock',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'role',
			'action'	 => 'lock',
		),
	),
	
	// Rename role
	'core_role_rename' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/role/rename',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'role',
			'action'	 => 'rename',
		),
	),
);
