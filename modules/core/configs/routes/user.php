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
 * Define routes for managing users
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Check if an email is already taken or not
	'core_user_checkemail' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/user/checkemail',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'checkemail',
		),
	),
	
	// Check if an username is already taken or not
	'core_user_checkusername' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/user/checkusername',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'checkusername',
		),
	),

	////////// BACKEND ACTIONS //////////
	// Activate or deactivate user
	'core_user_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/activate',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'activate',
		),
	),
	
	// Add new user
	'core_user_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/add',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'add',
		),
	),
	
	// Update user's avatar
	'core_user_avatar' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/avatar',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'avatar',
		),
	),
	
	// Delete user
	'core_user_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'delete',
		),
	),
	
	// Edit user
	'core_user_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/edit',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'edit',
		),
	),
	
	// List users
	'core_user_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'list',
		),
	),
	
	// Move user to other group
	'core_user_move' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/user/move',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'user',
			'action'	 => 'move',
		),
	),
);
