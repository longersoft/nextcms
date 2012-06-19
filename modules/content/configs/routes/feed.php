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
 * @version		2012-05-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for showing RSS/Atom feeds
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// View the latest articles in given category in RSS/Atom format
	'content_feed_category' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/feed/category/(\w+)/(\w+)/(\w+).xml',
		'reverse'  => 'content/feed/category/%s/%s/%s.xml',
		'map'	   => array(
			'1' => 'type',
			'2' => 'feed_format',
			'3' => 'category_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'feed',
			'action'	 => 'index',
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'feed.index.byCategory',
				'params'		 => array(
					'type' => array(
						'name'	   => 'type',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'feed_format' => array(
						'name'	   => 'feed_format',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'category_id' => array(
						'name'	   => 'category_id',
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
				),
				'type'		 => 'default',
				'default'	 => 'content/feed/category/{type}/{feed_format}/{category_id}.xml',
				'predefined' => array(
					'content/feed/category/{type}/{feed_format}/{category_id}-{slug}.xml'
				),
			),
		),
	),
	
	// View the latest activated articles in RSS/Atom format
	'content_feed_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/feed/index/(\w+)/(\w+).xml',
		'reverse'  => 'content/feed/index/%s/%s.xml',
		'map'	   => array(
			'1' => 'type',
			'2' => 'feed_format',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'feed',
			'action'	 => 'index',
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'feed.index.title',
				'params'		 => array(
					'type' => array(
						'name'	   => 'type',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'feed_format' => array(
						'name'	   => 'feed_format',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'	  => 'default',
				'default' => 'content/feed/index/{type}/{feed_format}.xml',
			),
		),
	),
	
	// View the latest articles of given user 
	'content_feed_user' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/feed/user/(\w+)/(\w+)/(\w+).xml',
		'reverse'  => 'content/feed/user/%s/%s/%s.xml',
		'map'	   => array(
			'1' => 'type',
			'2' => 'feed_format',
			'3' => 'user_name',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'feed',
			'action'	 => 'index',
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'feed.index.byUser',
				'params'		 => array(
					'type' => array(
						'name'	   => 'type',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'feed_format' => array(
						'name'	   => 'feed_format',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'user_name' => array(
						'name'	   => 'user_name',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'	  => 'default',
				'default' => 'content/feed/user/{type}/{feed_format}/{user_name}.xml',
			),
		),
	),
);
