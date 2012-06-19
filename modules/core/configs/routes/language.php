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
 * @version		2011-12-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing language files
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Add language item
	'core_language_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/language/add',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'language',
			'action'	 => 'add',
		),
	),

	// Delete language item
	'core_language_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/language/delete',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'language',
			'action'	 => 'delete',
		),
	),

	// Edit language file
	'core_language_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/language/edit',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'language',
			'action'	 => 'edit',
		),
	),

	// List language files
	'core_language_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/language/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'language',
			'action'	 => 'list',
		),
	),
);
