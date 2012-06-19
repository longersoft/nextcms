<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	configs
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing private message filters
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new filter
	'message_filter_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/filter/add',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'filter',
			'action'	 => 'add',
			'allowed'	 => true,
		),
	),
	
	// Delete filter
	'message_filter_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/filter/delete',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'filter',
			'action'	 => 'delete',
			'allowed'	 => true,
		),
	),

	// List filters
	'message_filter_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/filter/list',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'filter',
			'action'	 => 'list',
			'allowed'	 => true,
		),
	),
);
