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
 * @version		2012-03-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for viewing pages
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// View page details
	'content_page_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/page/view/(\w+)',
		'reverse'  => 'content/page/view/%s/',
		'map'	   => array(
			'1' => 'article_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'page',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'page.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'page.view.title',
				'params'		 => array(
					'article_id' => array(
						'name'	   => 'article_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'slug' => array(
						'name'	   => 'slug',
						'regex'	   => '([\w-_]+)',
						'reverse'  => '%s',
						'required' => false,
					),
					'year' => array(
						'name'	   => 'year',
						'regex'	   => '(\d+)',
						'reverse'  => '%d',
						'required' => false,
					),
					'month' => array(
						'name'	   => 'month',
						'regex'	   => '(\d+)',
						'reverse'  => '%d',
						'required' => false,
					),
					'day' => array(
						'name'	   => 'day',
						'regex'	   => '(\d+)',
						'reverse'  => '%d',
						'required' => false,
					),
				),
				'type'		 => 'default',
				'default'	 => 'content/page/view/{article_id}',
				'predefined' => array(
					'content/page/{article_id}-{slug}.html',
				),
			),
		),
	),
);
