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
 * @version		2012-02-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for registration
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Activate account (the link is sent to user's email)
	'core_registration_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'core/registration/activate/(\w+)',
		'reverse'  => 'core/registration/activate/%s',
		'map'	   => array(
			'1' => 'activation_key',
		),
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'registration',
			'action'	 => 'activate',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'registration.activate.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'registration.activate.title',
				'params'		 => array(
					'activation_key' => array(
						'name'	   => 'activation_key',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'core/registration/activate/{activation_key}',
				'predefined'	 => array(
					'activate/{activation_key}',
				),
			),
		),
	),

	// Register new account
	'core_registration_register' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'core/registration/register',
		'defaults' => array(
			'module'	 => 'core',
			'controller' => 'registration',
			'action'	 => 'register',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'registration.register.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'registration.register.title',
				'params'		 => array(),
				'type'			 => 'default',
				'default'		 => 'core/registration/register',
				'predefined'	 => array(
					'register',
				),
			),
		),
	),
);
