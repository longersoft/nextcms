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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Services_Feed
{
	/**
	 * Gets the latest activated articles in RSS/Atom format
	 * 
	 * @param string $type Type of articles
	 * @param string $format Format of feed entries. Can be "rss" or "atom"
	 * @param string $categoryId Id of category
	 * @param string $userName Username of article's author
	 * @return string
	 */
	public static function getArticleFeeds($type = Content_Models_Article::TYPE_ARTICLE, $format = 'rss', $categoryId = null, $userName = null)
	{
		Core_Services_Db::connect('slave');
		
		// Build searching criteria
		$limit	  = Core_Services_Config::get('core', 'feed_limit', 20);
		$criteria = array(
			'status'	  => Content_Models_Article::STATUS_ACTIVATED,
			'type'		  => $type,
			'category_id' => $categoryId,
		);
		if ($userName) {
			$users = Core_Services_User::find(array('user_name' => $userName));
			if ($users && count($users) > 0) {
				$criteria['created_user'] = $users[0]->user_id;
			}
		}
		
		$articles = Content_Services_Article::find($criteria, 0, $limit);
		
		// Create feed entries
		$view 	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$entries = array();
		if ($articles && count($articles) > 0) {
			foreach ($articles as $article) {
				$link 		 = $view->serverUrl() . $article->getViewUrl();		
				$description = $article->description;
				$image 		 = $article->getCover('thumbnail');
				$description = (null == $image || '' == $image) 
								? $description
								: '<a href="' . $link . '" title="' . addslashes($article->title) . '"><img src="' . $image . '" title="' . addslashes($article->title) . '" /></a>' . $description;
				$entries[] 	 = array(
									'title'		  => $article->title,
									'guid'		  => $link, 
									'link'		  => $link,
									'description' => $description,
									'content'	  => self::_formatContent($article),
									'lastUpdate'  => strtotime($article->activated_date),
								);
			}
		}
		
		// Generate feed output
		$category  = $categoryId ? Category_Services_Category::getById($categoryId) : null;
		$link	   = $category
				   ? $view->serverUrl() . $view->url($category->getProperties(), (Content_Models_Article::TYPE_BLOG == $type) ? 'content_blog_category' : 'content_article_category')
				   : Core_Services_Config::get('core', 'url_base', $view->serverUrl());
		$buildDate = strtotime(date('D, d M Y h:i:s'));
		$data 	   = array(
						'title'		  => $category ? $category->name : Core_Services_Config::get('core', 'feed_title', ''),
						'link'		  => $link,
						'description' => $category ? $category->name : Core_Services_Config::get('core', 'feed_description', ''),
						'copyright'   => Core_Services_Config::get('core', 'feed_copyright', ''),
						'generator'   => Core_Services_Config::get('core', 'feed_generator', Core_Services_Version::getVersion()),
						'lastUpdate'  => $buildDate,
						'published'   => $buildDate,
						'charset' 	  => 'UTF-8',
						'entries' 	  => $entries,
					);
		$feed 	   = Zend_Feed::importArray($data, $format);
		$xmlFeed   = $feed->saveXML();
		return $xmlFeed;
	}
	
	/**
	 * Formats the content of article for building feed entry
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return string
	 */
	private static function _formatContent($article)
	{
		// Because the article's content can contain widgets, I need to remove these widget's outputs
		return Core_Filters_WidgetParser::removeWidgets($article->content);
	}
}
