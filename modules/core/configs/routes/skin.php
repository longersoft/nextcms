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
 * Define routes for managing skins
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Activate the skin
	'core_skin_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/skin/activate',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'skin',
			'action'	 => 'activate',
		),
	),

	// Edit the CSS file
	'core_skin_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/skin/edit',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'skin',
			'action'	 => 'edit',
		),
	),

	// List skins of given template
	'core_skin_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/skin/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'skin',
			'action'	 => 'list',
		),
	),
);
