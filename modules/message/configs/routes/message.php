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
 * Define routes for managing private messages
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Delete message
	'message_message_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/delete',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'delete',
			'allowed'	 => true,
		),
	),

	// Empty trash
	'message_message_empty' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/empty',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'empty',
			'allowed'	 => true,
		),
	),	

	// List private messages
	'message_message_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/list',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'list',
			'allowed'	 => true,
		),
	),
	
	// Mark message as read/unread
	'message_message_mark' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/mark',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'mark',
			'allowed'	 => true,
		),
	),
	
	// Move to other folder
	'message_message_move' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/move',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'move',
			'allowed'	 => true,
		),
	),
	
	// Send private message
	'message_message_send' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/send',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'send',
			'allowed'	 => true,
		),
	),
	
	// Add/remove star
	'message_message_star' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/star',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'star',
			'allowed'	 => true,
		),
	),
	
	// View private messages in a thread
	'message_message_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/message/view',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'message',
			'action'	 => 'view',
			'allowed'	 => true,
		),
	),
);
