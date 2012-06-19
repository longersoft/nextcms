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
 * Define routes for managing articles
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// View articles by date
	'content_article_archive' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/archive/((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
		'reverse'  => 'content/article/archive/%s/',
		'map'	   => array(
			'1' => 'date',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'archive',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.archive.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.archive.title',
				'params'		 => array(
					'date' => array(
						'name'	   => 'date',
						'regex'	   => '((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
						'reverse'  => '%s',
						'required' => true,
					),
				),
				'type'		 => 'default',
				'default'	 => 'content/article/archive/{year}/{month}',
			),
		),
	),
	
	// View articles in given category by date
	'content_article_archive_category' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/archive/(\w+)/((\d{4}-\d{2}-\d{1,2}|\d{4}-\d{2}|\d{4})+)',
		'reverse'  => 'content/article/archive/%s/%s/',
		'map'	   => array(
			'1' => 'category_id',
			'2' => 'date',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'archive',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.archive.category',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.archive.category',
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
				'default'	 => 'content/article/archive/{category_id}/{year}/{month}',
			),
		),
	),

	// View articles in a given category
	'content_article_category' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/category/(\w+)',
		'reverse'  => 'content/article/category/%s/',
		'map'	   => array(
			'1' => 'category_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'category',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.category.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.category.title',
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
				'default'	 => 'content/article/category/{category_id}',
				'predefined' => array(
					'content/article/category/{slug}',
					'content/article/category/{category_id}-{slug}'
				),
			),
		),
	),
	
	// View articles in a given category (pager)
	'content_article_category_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/category/(\w+)/page-(\d+)',
		'reverse'  => 'content/article/category/%s/page-%s',
		'map'	   => array(
			'1' => 'category_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'category',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.category.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.category.pager',
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
				'default'	 => 'content/article/category/{category_id}/page-{page}',
				'predefined' => array(
					'content/article/category/{slug}/page-{page}',
					'content/article/category/{category_id}-{slug}/page-{page}'
				),
			),
		),
	),
	
	// Search for articles
	'content_article_search' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => 'content/article/search',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'search',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.search.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.search.title',
				'params'		 => array(),
				'type'		 	 => 'default',
				'default'	 	 => 'content/article/search',
			),
		),
	),
	
	// View articles by given tag
	'content_article_tag' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/tag/(\w+)',
		'reverse'  => 'content/article/tag/%s/',
		'map'	   => array(
			'1' => 'tag_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.tag.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.tag.title',
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
				'default'	 => 'content/article/tag/{tag_id}',
				'predefined' => array(
					'content/article/tag/{slug}',
					'content/article/tag/{tag_id}-{slug}'
				),
			),
		),
	),
	
	// View articles by given tag (pager)
	'content_article_tag_pager' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/tag/(\w+)/page-(\d+)',
		'reverse'  => 'content/article/tag/%s/page-%s',
		'map'	   => array(
			'1' => 'tag_id',
			'2' => 'page',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'tag',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.tag.pager',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.tag.pager',
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
				'default'	 => 'content/article/tag/{tag_id}/page-{page}',
				'predefined' => array(
					'content/article/tag/{slug}/page-{page}',
					'content/article/tag/{tag_id}-{slug}/page-{page}'
				),
			),
		),
	),
	
	// View article details
	'content_article_view' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'content/article/view/(\w+)/(\w+)',
		'reverse'  => 'content/article/view/%s/%s/',
		'map'	   => array(
			'1' => 'category_id',
			'2' => 'article_id',
		),
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'view',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'article.view.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'article.view.title',
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
				'default'	 => 'content/article/view/{category_id}/{article_id}',
				'predefined' => array(
					'content/{category_id}/{article_id}-{slug}.html',
					'content/{year}/{month}/{category_id}/{article_id}-{slug}.html',
				),
			),
		),
	),
	
	////////// BACKEND ACTIONS //////////
	// Activate or deactivate article
	'content_article_activate' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/activate',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'activate',
		),
	),
	
	// Add new article
	'content_article_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/add',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'add',
		),
	),
	
	// Helper action: Count the number of articles by status
	'content_article_count' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/count',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'count',
			'allowed'	 => true,
		),
	),
	
	// Update article's cover
	'content_article_cover' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/cover',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'cover',
		),
	),
	
	// Delete article
	'content_article_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/delete',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'delete',
		),
	),
	
	// Edit article
	'content_article_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/edit',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'edit',
		),
	),
	
	// Empty trash
	'content_article_empty' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/empty',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'empty',
		),
	),
	
	// List articles
	'content_article_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/list',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'list',
		),
	),
	
	// Move article to other category
	'content_article_move' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/move',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'move',
		),
	),
	
	// Save order of articles
	'content_article_order' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/order',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'order',
		),
	),
	
	// Check if the slug is already taken
	'content_article_slug' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/content/article/slug',
		'defaults' => array(
			'module'	 => 'content',
			'controller' => 'article',
			'action'	 => 'slug',
			'allowed'	 => true,
		),
	),
);
