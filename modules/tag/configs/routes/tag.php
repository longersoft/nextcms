<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	configs
 * @since		1.0
 * @version		2012-01-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing tags
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add new tag
	'tag_tag_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/tag/add',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'tag',
			'action'	 => 'add',
		),
	),
	
	// Delete tag
	'tag_tag_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/tag/delete',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'tag',
			'action'	 => 'delete',
		),
	),
	
	// Edit tag
	'tag_tag_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/tag/edit',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'tag',
			'action'	 => 'edit',
		),
	),
	
	// List tags
	'tag_tag_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/tag/list',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'tag',
			'action'	 => 'list',
		),
	),
	
	// Suggest tags
	'tag_tag_suggest' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/tag/suggest',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'tag',
			'action'	 => 'suggest',
			'allowed'	 => true,
		),
	),
	
	// Validate a tag
	'tag_tag_validate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/tag/tag/validate',
		'defaults' => array(
			'module'	 => 'tag',
			'controller' => 'tag',
			'action'	 => 'validate',
			'allowed'	 => true,
		),
	),
);
