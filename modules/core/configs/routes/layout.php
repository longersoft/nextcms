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
 * Define routes for managing layout scripts
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Edit the layout script
	'core_layout_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/layout/edit',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'layout',
			'action'	 => 'edit',
		),
	),

	// List layouts of given template
	'core_layout_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/layout/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'layout',
			'action'	 => 'list',
		),
	),
);
