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
 * Define routes for managing permissions
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// List the permissions of role
	'core_rule_role' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/rule/role',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'rule',
			'action'	 => 'role',
		),
	),
	
	// List the permissions of user
	'core_rule_user' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/rule/user',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'rule',
			'action'	 => 'user',
		),
	),
);
