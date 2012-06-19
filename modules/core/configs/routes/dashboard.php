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
 * Define routes for managing user's dashboard
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Show user's dashboard
	'core_dashboard_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => '{adminPrefix}([/dashboard]*)',
		'reverse'  => '{adminPrefix}/dashboard',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'dashboard',
			'action'	 => 'index',
			'allowed'	 => true,
		),
	),
);
