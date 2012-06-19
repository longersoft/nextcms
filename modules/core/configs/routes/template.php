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
 * @version		2012-02-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing templates
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Activate a template
	'core_template_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/template/activate',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'template',
			'action'	 => 'activate',
		),
	),
	
	// Install a template
	'core_template_install' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/template/install',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'template',
			'action'	 => 'install',
		),
	),

	// List templates
	'core_template_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/template/list',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'template',
			'action'	 => 'list',
		),
	),
	
	// Uninstall a template
	'core_template_uninstall' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/template/uninstall',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'template',
			'action'	 => 'uninstall',
		),
	),
);
