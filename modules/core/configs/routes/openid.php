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
 * Define routes for managing user's OpenId URLs
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new OpenID URL
	'core_openid_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/openid/add',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'openid',
			'action'	 => 'add',
			'allowed'	 => true,
		),
	),
	
	// Delete OpenID URL
	'core_openid_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/openid/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'openid',
			'action'	 => 'delete',
			'allowed'	 => true,
		),
	),

	// List OpenID URLs
	'core_openid_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/openid/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'openid',
			'action'	 => 'list',
			'allowed'	 => true,
		),
	),
);
