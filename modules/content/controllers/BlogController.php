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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-05-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_BlogController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Lists blog entries by date
	 * 
	 * @return void
	 */
	public function archiveAction()
	{
		Core_Services_Db::connect('slave');
		
		$request	= $this->getRequest();
		$categoryId = $request->getParam('category_id');
		$slug		= $request->getParam('slug');
		$language   = $request->getParam('lang');
		$page		= $request->getParam('page', 1);
		$d			= $request->getParam('date', date('Y-m'));
		
		$category   = null;
		if ($categoryId || $slug) {
			$category = $categoryId
					  ? Category_Services_Category::getById($categoryId)
					  : Category_Services_Category::getBySlug($slug, 'content', $language);
		}
		$request->setParam('category_id', $category ? $category->category_id : $categoryId);
		
		$year  = date('Y', strtotime($d));
		$month = date('m', strtotime($d));
		$day   = (int) date('d', strtotime($d));
		$date  = date('Y-m-d', strtotime($d));
		
		// Define the lower and upper dates
		$lowerDate = $upperDate = null;
		if ($date == $d) {
			$lowerDate = $date;
			$upperDate = date('Y-m-d', strtotime('+1 day', strtotime($date)));
		} else {
			$lowerDate = $year . '-' . $month . '-01';
			$upperDate = $year . '-' . $month . '-31';
		}
		
		$criteria = array(
			'category_id' => $categoryId,
			'status'	  => Content_Models_Article::STATUS_ACTIVATED,
			'type'		  => Content_Models_Article::TYPE_BLOG,
		);
		$articles = Content_Services_Article::findByDate($criteria, $lowerDate, $upperDate);
		$this->view->assign('articles', $articles);
	}
	
	/**
	 * Lists blog entries in given category
	 * 
	 * @return void
	 */
	public function categoryAction()
	{
		Core_Services_Db::connect('slave');
		
		$request	= $this->getRequest();
		$categoryId = $request->getParam('category_id');
		$slug		= $request->getParam('slug');
		$language   = $request->getParam('lang');
		$page		= $request->getParam('page', 1);
		
		$category	= $categoryId
					? Category_Services_Category::getById($categoryId)
					: Category_Services_Category::getBySlug($slug, 'content', $language);
		if ($category == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the category');
		}
		$request->setParam('category_id', $category->category_id);
		
		// Show RSS link in the head section
		$this->view->headLink(array(
			'rel' 	=> 'alternate', 
			'type' 	=> 'application/rss+xml', 
			'href' 	=> $this->view->serverUrl() . $this->view->url(array_merge(array('feed_format' => 'atom', 'type' => Content_Models_Article::TYPE_BLOG), $category->getProperties()), 'content_feed_category'),
		));
		if ($category->meta_keyword) {
			$this->view->headMeta()->setName('keyword', strip_tags($category->meta_keyword));
		}
		$this->view->headMeta()->setName('description', $category->meta_description ? strip_tags($category->meta_description) : $category->name);
		
		$criteria = array(
			'status'	  => Content_Models_Article::STATUS_ACTIVATED,
			'type'		  => Content_Models_Article::TYPE_BLOG,
			'category_id' => $category->category_id,
		);
		$perPage  = 10;
		$articles = Content_Services_Article::find($criteria, ($page - 1) * $perPage, $perPage);
		$total    = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $category->getProperties()), 'content_article_category_pager');
		
		$timeDiffFormats = array(
			'DAY'			=> $this->view->translator()->_('global._share.daysAgo'),
			'DAY_HOUR'		=> $this->view->translator()->_('global._share.daysHoursAgo'),
			'HOUR'			=> $this->view->translator()->_('global._share.hoursAgo'),
			'HOUR_MINUTE'	=> $this->view->translator()->_('global._share.hoursMinutesAgo'),
			'MINUTE'		=> $this->view->translator()->_('global._share.minutesAgo'),
			'MINUTE_SECOND'	=> $this->view->translator()->_('global._share.minutesSecondsAgo'),
			'SECOND'		=> $this->view->translator()->_('global._share.secondsAgo'),
		);
		
		$this->view->assign(array(
			'category'		  => $category,
			'articles'		  => $articles,
			'numArticles'	  => $total,
			'paginator'		  => $this->view->paginator('sliding')->render($paginator, $pagerPath),
			'timeDiffFormats' => $timeDiffFormats,
		));
	}	
	
	/**
	 * Homepage of blog
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$language = $request->getParam('lang');
		$page	  = $request->getParam('page', 1);
		
		// Show RSS link in the head section
		$this->view->headLink(array(
			'rel' 	=> 'alternate', 
			'type' 	=> 'application/rss+xml', 
			'href' 	=>  $this->view->serverUrl() . $this->view->url(array('feed_format' => 'atom', 'type' => Content_Models_Article::TYPE_BLOG), 'content_feed_index'),
		));
		
		$criteria = array(
			'status' => Content_Models_Article::STATUS_ACTIVATED,
			'type'	 => Content_Models_Article::TYPE_BLOG,
		);
		$perPage  = 10;
		$articles = Content_Services_Article::find($criteria, ($page - 1) * $perPage, $perPage);
		$total    = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array('page' => '__PAGE__'), 'content_blog_index_pager');
		
		$timeDiffFormats = array(
			'DAY'			=> $this->view->translator()->_('global._share.daysAgo'),
			'DAY_HOUR'		=> $this->view->translator()->_('global._share.daysHoursAgo'),
			'HOUR'			=> $this->view->translator()->_('global._share.hoursAgo'),
			'HOUR_MINUTE'	=> $this->view->translator()->_('global._share.hoursMinutesAgo'),
			'MINUTE'		=> $this->view->translator()->_('global._share.minutesAgo'),
			'MINUTE_SECOND'	=> $this->view->translator()->_('global._share.minutesSecondsAgo'),
			'SECOND'		=> $this->view->translator()->_('global._share.secondsAgo'),
		);
		
		$this->view->assign(array(
			'articles'		  => $articles,
			'numArticles'	  => $total,
			'paginator'		  => $this->view->paginator('sliding')->render($paginator, $pagerPath),
			'timeDiffFormats' => $timeDiffFormats,
		));
	}
	
	/**
	 * Searches for blog entries
	 * 
	 * @return void
	 */
	public function searchAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$keyword  = $request->getParam('q');
		$page	  = $request->getParam('page', 1);
		
		// Filter the keyword
		$keyword  = strip_tags($keyword);
		$keyword  = Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterSearchingKeyword', $keyword);
		$criteria = array(
			'status'  => Content_Models_Article::STATUS_ACTIVATED,
			'type'    => Content_Models_Article::TYPE_BLOG,
			'keyword' => $keyword,
		);
		
		$perPage  = 10;
		$articles = Content_Services_Article::find($criteria, ($page - 1) * $perPage, $perPage);
		$total    = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array(), 'content_blog_search') . '?page=__PAGE__&q=' . $keyword;
		
		$timeDiffFormats = array(
			'DAY'			=> $this->view->translator()->_('global._share.daysAgo'),
			'DAY_HOUR'		=> $this->view->translator()->_('global._share.daysHoursAgo'),
			'HOUR'			=> $this->view->translator()->_('global._share.hoursAgo'),
			'HOUR_MINUTE'	=> $this->view->translator()->_('global._share.hoursMinutesAgo'),
			'MINUTE'		=> $this->view->translator()->_('global._share.minutesAgo'),
			'MINUTE_SECOND'	=> $this->view->translator()->_('global._share.minutesSecondsAgo'),
			'SECOND'		=> $this->view->translator()->_('global._share.secondsAgo'),
		);
		
		// Highlight the title if it matches the keyword
		Core_Base_Hook_Registry::getInstance()->register('Content_FilterArticleTitle', array(Core_Filters_Highlight::getInstance(), 'filter'));
		
		$this->view->assign(array(
			'articles'		  => $articles,
			'numArticles'	  => $total,
			'keyword'		  => $criteria['keyword'],
			'paginator'		  => $this->view->paginator('sliding')->render($paginator, $pagerPath),
			'timeDiffFormats' => $timeDiffFormats,
		));
	}
	
	/**
	 * Lists blog entries by given tags
	 * 
	 * @return void
	 */
	public function tagAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$tagId    = $request->getParam('tag_id');
		$slug	  = $request->getParam('slug');
		$language = $request->getParam('lang');
		$page	  = $request->getParam('page', 1);
		
		if ($tagId) {
			$tag = Tag_Services_Tag::getById($tagId);
		} elseif ($slug) {
			$result = Tag_Services_Tag::find(array(
				'slug'	   => $slug,
				'language' => $language,
			), 0, 1);
			$tag	= ($request && count($result) > 0) ? $result[0] : null;
		}
		if ($tag == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the tag');
		}
		
		$perPage   = 10;
		$criteria  = array(
			'status' => Content_Models_Article::STATUS_ACTIVATED,
			'type'   => Content_Models_Article::TYPE_BLOG,
			'tag'	 => $tag,
		);
		$articles  = Content_Services_Article::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	   = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $tag->getProperties()), 'content_blog_tag_pager');
		
		$timeDiffFormats = array(
			'DAY'			=> $this->view->translator()->_('global._share.daysAgo'),
			'DAY_HOUR'		=> $this->view->translator()->_('global._share.daysHoursAgo'),
			'HOUR'			=> $this->view->translator()->_('global._share.hoursAgo'),
			'HOUR_MINUTE'	=> $this->view->translator()->_('global._share.hoursMinutesAgo'),
			'MINUTE'		=> $this->view->translator()->_('global._share.minutesAgo'),
			'MINUTE_SECOND'	=> $this->view->translator()->_('global._share.minutesSecondsAgo'),
			'SECOND'		=> $this->view->translator()->_('global._share.secondsAgo'),
		);
		
		$this->view->assign(array(
			'tag'		 	  => $tag,
			'articles'		  => $articles,
			'numArticles'	  => $total,
			'paginator'		  => $this->view->paginator('sliding')->render($paginator, $pagerPath),
			'timeDiffFormats' => $timeDiffFormats,
		));
	}
	
	/**
	 * Views blog entry details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$articleId = $request->getParam('article_id');
		$slug	   = $request->getParam('slug');
		$language  = $request->getParam('lang');
		
		if ($articleId) {
			$article = Content_Services_Article::getById($articleId);
		} elseif ($slug) {
			$result  = Content_Services_Article::find(array(
				'slug'	 => $slug,
				'type'   => Content_Models_Article::TYPE_BLOG,
				'status' => Content_Models_Article::STATUS_ACTIVATED,
			), 0, 1);
			$article = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($article == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the blog entry');
		}
		$request->setParam('category_id', $article->category_id)
				->setParam('user_id', $article->created_user)
				->setParam('entity_class', get_class($article))
				->setParam('entity_id', $article->article_id);
		
		// Set the meta tags
		$keywords = array();
		if ($article->meta_keyword) {
			$keywords[] = strip_tags($article->meta_keyword);
		}
		$tags = Content_Services_Article::getTags($article);
		if ($tags) {
			foreach ($tags as $tag) {
				$keywords[] = $tag->title;
			}
		}
		
		if (count($keywords) > 0) {
			$this->view->headMeta()->setName('keywords', implode(',', $keywords));
		}
		$this->view->headMeta()->setName('description', $article->meta_description ? strip_tags($article->meta_description) : $article->description);
		
		// Show RSS link
		if ($article->category_id && ($category = Category_Services_Category::getById($article->category_id))) {
			$this->view->headLink(array(
				'rel'  => 'alternate',
				'type' => 'application/rss+xml', 
				'href' => $this->view->serverUrl() . $this->view->url(array_merge(array('feed_format' => 'atom', 'type' => Content_Models_Article::TYPE_BLOG), $category->getProperties()), 'content_feed_category'),
			));
		}
		
		// Highlight the search keyword in the title and content if they match the keyword
		$referer   = $this->getRequest()->getServer('HTTP_REFERER');
		$searchUrl = $this->view->serverUrl() . $this->view->url(array(), 'content_blog_search');
		if ($referer && substr($referer, 0, strlen($searchUrl)) == $searchUrl) {
			Core_Base_Hook_Registry::getInstance()->register('Content_FilterArticleTitle', array(Core_Filters_Highlight::getInstance(), 'filter'))
												  ->register('Content_FilterArticleContent', array(Core_Filters_Highlight::getInstance(), 'filter'));
		}
		
		// Filter the article's content 
		$article->content = Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleContent', $article->content);
		
		$this->view->assign(array(
			'article' => $article,
			'tags'	  => $tags,
		));
		
		// Update the number of views
		Core_Services_Counter::register($article, 'views', 'Content_Services_Article::increaseNumViews', array($article));
	}
}
