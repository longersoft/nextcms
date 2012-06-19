<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing polls
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new poll
	'poll_poll_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/poll/poll/add',
		'defaults' => array(
			'module'	 => 'poll',
			'controller' => 'poll',
			'action'	 => 'add',
		),
	),
	
	// Delete poll
	'poll_poll_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/poll/poll/delete',
		'defaults' => array(
			'module'	 => 'poll',
			'controller' => 'poll',
			'action'	 => 'delete',
		),
	),
	
	// Edit poll
	'poll_poll_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/poll/poll/edit',
		'defaults' => array(
			'module'	 => 'poll',
			'controller' => 'poll',
			'action'	 => 'edit',
		),
	),
	
	// List polls
	'poll_poll_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/poll/poll/list',
		'defaults' => array(
			'module'	 => 'poll',
			'controller' => 'poll',
			'action'	 => 'list',
		),
	),
);
