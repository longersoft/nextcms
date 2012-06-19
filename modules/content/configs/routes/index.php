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
 * @version		2012-05-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define index route
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Homepage of the Content module
	'content_index_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'content',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'index',
			'action'	 => 'index',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'index.index.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'index.index.title',
				'params'		 => array(),
				'type'		 	 => 'default',
				'default'	 	 => 'content',
			),
		),
	),	
);
