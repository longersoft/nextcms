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
 * Define route for the home page
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Show the homepage
	'core_index_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '/',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'index',
			'action'	 => 'index',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'index.index.title',
			),
		),
	),
);
