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
 * Define routes for viewing blog entries
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// List blog entries by date
	'content_blog_archive' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/archive/((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
		'reverse'  => 'content/blog/archive/%s/',
		'map'	   => array(
			'1' => 'date',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'archive',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.archive.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.archive.title',
				'params'		 => array(
					'date' => array(
						'name'	   => 'date',
						'regex'	   => '((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'content/blog/archive/{year}/{month}',
			),
		),
	),
	
	// List blog entries in given category by date
	'content_blog_archive_category' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/archive/(\w+)/((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
		'reverse'  => 'content/blog/archive/%s/%s/',
		'map'	   => array(
			'1' => 'category_id',
			'2' => 'date',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'archive',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.archive.category',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.archive.category',
				'params'		 => array(
					'category_id' => array(
						'name'	   => 'category_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'date' => array(
						'name'	   => 'date',
						'regex'	   => '((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
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
				'default'	 => 'content/blog/archive/{category_id}/{year}/{month}',
			),
		),
	),

	// Blog homepage
	'content_blog_index' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'content/blog',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'index',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.index.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.index.title',
				'params'		 => array(),
				'type'			 => 'default',
				'default'		 => 'content/blog',
				'predefined'	 => array(),
			),
		),
	),
	
	// Blog homepage (pager)
	'content_blog_index_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/page-(\d+)',
		'reverse'  => 'content/blog/page-%s/',
		'map'	   => array(
			'1' => 'page',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'index',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.index.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.index.pager',
				'params'		 => array(
					'page' => array(
						'name'	   => 'page',
						'regex'	   => '(\d+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'			 => 'default',
				'default'		 => 'content/blog/page-{page}',
				'predefined'	 => array(),
			),
		),
	),
	
	// View blog entries in a given category
	'content_blog_category' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/category/(\w+)',
		'reverse'  => 'content/blog/category/%s/',
		'map'	   => array(
			'1' => 'category_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'category',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.category.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.category.title',
				'params'		 => array(
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
				'default'	 => 'content/blog/category/{category_id}',
				'predefined' => array(
					'content/blog/category/{slug}',
					'content/blog/category/{category_id}-{slug}'
				),
			),
		),
	),
	
	// View blog entries in a given category (pager)
	'content_blog_category_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/category/(\w+)/page-(\d+)',
		'reverse'  => 'content/blog/category/%s/page-%s',
		'map'	   => array(
			'1' => 'category_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'category',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.category.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.category.pager',
				'params'		 => array(
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
					'page' => array(
						'name'	   => 'page',
						'regex'	   => '(\d+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'content/blog/category/{category_id}/page-{page}',
				'predefined' => array(
					'content/blog/category/{slug}/page-{page}',
					'content/blog/category/{category_id}-{slug}/page-{page}'
				),
			),
		),
	),	
	
	// Search for blog entries
	'content_blog_search' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'content/blog/search',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'search',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.search.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.search.title',
				'params'		 => array(),
				'type'		 	 => 'default',
				'default'	 	 => 'content/blog/search',
			),
		),
	),
	
	// View blog entries by given tag
	'content_blog_tag' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/tag/(\w+)',
		'reverse'  => 'content/blog/tag/%s/',
		'map'	   => array(
			'1' => 'tag_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.tag.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.tag.title',
				'params'		 => array(
					'tag_id' => array(
						'name'	   => 'tag_id',
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
				'default'	 => 'content/blog/tag/{tag_id}',
				'predefined' => array(
					'content/blog/tag/{slug}',
					'content/blog/tag/{tag_id}-{slug}'
				),
			),
		),
	),
	
	// View blog entries by given tag (pager)
	'content_blog_tag_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/tag/(\w+)/page-(\d+)',
		'reverse'  => 'content/blog/tag/%s/page-%s',
		'map'	   => array(
			'1' => 'tag_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.tag.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.tag.pager',
				'params'		 => array(
					'tag_id' => array(
						'name'	   => 'tag_id',
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
					'page' => array(
						'name'	   => 'page',
						'regex'	   => '(\d+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'content/blog/tag/{tag_id}/page-{page}',
				'predefined' => array(
					'content/blog/tag/{slug}/page-{page}',
					'content/blog/tag/{tag_id}-{slug}/page-{page}'
				),
			),
		),
	),	

	// View blog entry details
	'content_blog_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/blog/view/(\w+)/(\w+)',
		'reverse'  => 'content/blog/view/%s/%s/',
		'map'	   => array(
			'1' => 'category_id',
			'2' => 'article_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'blog',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'blog.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'blog.view.title',
				'params'		 => array(
					'article_id' => array(
						'name'	   => 'article_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'category_id' => array(
						'name'	   => 'category_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => false,
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
						'reverse'  => '%s',
						'required' => false,
					),
					'day' => array(
						'name'	   => 'day',
						'regex'	   => '(\d+)',
						'reverse'  => '%s',
						'required' => false,
					),
				),
				'type'		 => 'default',
				'default'	 => 'content/blog/view/{category_id}/{article_id}',
				'predefined' => array(
					'content/blog/{category_id}/{article_id}-{slug}.html',
					'content/blog/{year}/{month}/{category_id}/{article_id}-{slug}.html',
				),
			),
		),
	),
);
