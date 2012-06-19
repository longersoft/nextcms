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
 * Define routes for setting the permalinks
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Customize the links in the front-end section
	'core_permalink_config' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/core/permalink/config',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'permalink',
			'action'	 => 'config',
		),
	),
);
