<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing comments
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Activate or deactivate comment
	'comment_comment_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/activate',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'activate',
		),
	),
	
	// Delete comment
	'comment_comment_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/delete',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'delete',
		),
	),
	
	// Edit comment
	'comment_comment_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/edit',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'edit',
		),
	),
	
	// List comments
	'comment_comment_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/list',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'list',
		),
	),
	
	// Reply to comment
	'comment_comment_reply' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/reply',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'reply',
		),
	),
	
	// Report comment as a spam
	'comment_comment_spam' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/spam',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'spam',
		),
	),
	
	// View all comments in the thread
	'comment_comment_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/comment/comment/view',
		'defaults' => array(
			'module'	 => 'comment',
			'controller' => 'comment',
			'action'	 => 'view',
		),
	),
);
