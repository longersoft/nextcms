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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Widgets_Articles_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows categories
	 * 
	 * @return void
	 */
	public function categoryAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$language	= $request->getParam('language');
		$categories = Category_Services_Category::getTree('content', $language);
		$folders    = Category_Services_Folder::find(array(
			'entity_class' => 'Content_Models_Article',
			'language'	   => $language,
		));
		
		$array = array(
			'categories' => array(),
			'folders'	 => array(),
		);
		if ($categories) {
			foreach ($categories as $category) {
				$array['categories'][] = array(
					'category_id' => $category->category_id,
					'label'		  => str_repeat('---', $category->depth) . $category->name,
				);
			}
		}
		if ($folders) {
			foreach ($folders as $folder) {
				$array['folders'][] = $folder->getProperties(array('folder_id', 'name'));
			}
		}
		echo Zend_Json::encode($array);
	}
	
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request    = $this->getRequest();
		$language   = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$articleIds = $request->getParam('article_ids');
		$dataSource = $request->getParam('data_source');
		
		$articles   = array();
		if ($dataSource == 'set' && $articleIds) {
			$resultSet = Content_Services_Article::find(array(
				'article_ids' => $articleIds,
			));
			foreach ($resultSet as $article) {
				$articles[] = $article->getProperties(array('article_id', 'title', 'image_square'));
			}
		}
		
		$this->view->assign(array(
			'language'	 => $language,
			'languages'  => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
			'categories' => Category_Services_Category::getTree('content', $language),
			'folders'	 => Category_Services_Folder::find(array(
								'entity_class' => 'Content_Models_Article',
								'language'	   => $language,
							)),
			'uid'		 => uniqid(),
			'articles'   => $articles,
		));
	}
	
	/**
	 * Shows the articles
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request    = $this->getRequest();
		$criteria   = array(
			'status'	   => Content_Models_Article::STATUS_ACTIVATED,
			'type'		   => $request->getParam('type', Content_Models_Article::TYPE_ARTICLE),
			'language'	   => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
			'keyword'	   => $request->getParam('keyword', null),
			'category_id'  => $request->getParam('category_id', null),
			'featured'	   => $request->getParam('featured', null),
			'image_icon'   => $request->getParam('image_icon', null),
			'video_icon'   => $request->getParam('video_icon', null),
			'created_user' => $request->getParam('user_id', null),
		);
		$count      = $request->getParam('limit', 20);
		$dataSource = $request->getParam('data_source');
		$articles   = array();
		
		switch ($dataSource) {
			case 'most_commented':
				$criteria['sort_by']  = 'num_comments';
				$criteria['sort_dir'] = 'DESC';
				break;
			
			// Get the most viewed articles
			case 'most_viewed':
				$criteria['sort_by']  = 'num_views';
				$criteria['sort_dir'] = 'DESC';
				break;
				
			case 'most_rated':
				break;
			
			// Get the articles by their Ids
			case 'set':
				$articleIds = $request->getParam('article_ids', array());
				$criteria	= array(
					'article_ids' => $articleIds,
					'category_id' => null,
					'status'	  => Content_Models_Article::STATUS_ACTIVATED,
				);
				break;
			
			case 'folder':
				$criteria['category_id'] = null;
				$criteria['folder_id']	 = $request->getParam('folder_id');
				break;
			
			// Get the latest activated articles
			case 'latest':
			default:
				$criteria['sort_by']  = 'activated_date';
				$criteria['sort_dir'] = 'DESC';
				break;
		}
		$articles = Content_Services_Article::find($criteria, 0, $count);
		
		$timeDiffFormats = array(
			'DAY'			=> $this->view->translator()->_('show.daysAgo'),
			'DAY_HOUR'		=> $this->view->translator()->_('show.daysHoursAgo'),
			'HOUR'			=> $this->view->translator()->_('show.hoursAgo'),
			'HOUR_MINUTE'	=> $this->view->translator()->_('show.hoursMinutesAgo'),
			'MINUTE'		=> $this->view->translator()->_('show.minutesAgo'),
			'MINUTE_SECOND'	=> $this->view->translator()->_('show.minutesSecondsAgo'),
			'SECOND'		=> $this->view->translator()->_('show.secondsAgo'),
		);
		
		$this->view->assign(array(
			'title'			  => stripslashes($request->getParam('title', '')),
			'articles'		  => $articles,
			'type'			  => $criteria['type'],
			'numArticles'	  => $articles ? count($articles) : 0,
			'category'		  => $criteria['category_id'] ? Category_Services_Category::getById($criteria['category_id']) : null,
			'timeDiffFormats' => $timeDiffFormats,
		));
	}
}
