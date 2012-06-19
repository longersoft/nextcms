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
 * @subpackage	views
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing attachments of private message
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Delete attachment
	'message_attachment_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/attachment/delete',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'attachment',
			'action'	 => 'delete',
			'allowed'	 => true,
		),
	),
	
	// Download attachments
	'message_attachment_download' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/attachment/download',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'attachment',
			'action'	 => 'download',
			'allowed'	 => true,
		),
	),
	
	// Upload attachments
	'message_attachment_upload' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/message/attachment/upload',
		'defaults' => array(
			'module'	 => 'message',
			'controller' => 'attachment',
			'action'	 => 'upload',
			'allowed'	 => true,
		),
	),
);
