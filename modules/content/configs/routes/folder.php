<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	configs
 * @since		1.0
 * @version		2012-03-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing articles
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add an article to a folder
	'content_folder_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/folder/add',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'folder',
			'action'	 => 'add',
		),
	),
	
	// Remove an article from a folder
	'content_folder_remove' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/folder/remove',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'folder',
			'action'	 => 'remove',
		),
	),
);
