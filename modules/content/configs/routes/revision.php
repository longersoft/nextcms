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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing article's revisions
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Delete revision
	'content_revision_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/revision/delete',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'revision',
			'action'	 => 'delete',
		),
	),
	
	// List revisions of article
	'content_revision_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/revision/list',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'revision',
			'action'	 => 'list',
		),
	),
	
	// Restore revision
	'content_revision_restore' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/revision/restore',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'revision',
			'action'	 => 'restore',
		),
	),
	
	// View revision
	'content_revision_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/revision/view',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'revision',
			'action'	 => 'view',
		),
	),
);
