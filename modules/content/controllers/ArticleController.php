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

class Content_ArticleController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Views articles by date
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
			'type'		  => Content_Models_Article::TYPE_ARTICLE,
		);
		$articles = Content_Services_Article::findByDate($criteria, $lowerDate, $upperDate);
		$this->view->assign('articles', $articles);
	}
	
	/**
	 * Categorizes articles
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
			'href' 	=> $this->view->serverUrl() . $this->view->url(array_merge(array('feed_format' => 'atom', 'type' => Content_Models_Article::TYPE_ARTICLE), $category->getProperties()), 'content_feed_category'),
		));
		if ($category->meta_keyword) {
			$this->view->headMeta()->setName('keyword', strip_tags($category->meta_keyword));
		}
		$this->view->headMeta()->setName('description', $category->meta_description ? strip_tags($category->meta_description) : $category->name);
		
		$criteria = array(
			'status'	  => Content_Models_Article::STATUS_ACTIVATED,
			'type'		  => Content_Models_Article::TYPE_ARTICLE,
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
	 * Searches for articles
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
			'type'    => Content_Models_Article::TYPE_ARTICLE,
			'keyword' => $keyword,
		);
		
		$perPage  = 10;
		$articles = Content_Services_Article::find($criteria, ($page - 1) * $perPage, $perPage);
		$total    = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array(), 'content_article_search') . '?page=__PAGE__&q=' . $keyword;
		
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
	 * Views articles by given tags
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
			'type'   => Content_Models_Article::TYPE_ARTICLE,
			'tag'	 => $tag,
		);
		$articles  = Content_Services_Article::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	   = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
		$pagerPath = $this->view->url(array_merge(array('page' => '__PAGE__'), $tag->getProperties()), 'content_article_tag_pager');
		
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
	 * Views article details
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
				'type'   => Content_Models_Article::TYPE_ARTICLE,
				'status' => Content_Models_Article::STATUS_ACTIVATED,
			), 0, 1);
			$article = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($article == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the article');
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
				'href' => $this->view->serverUrl() . $this->view->url(array_merge(array('feed_format' => 'atom', 'type' => Content_Models_Article::TYPE_ARTICLE), $category->getProperties()), 'content_feed_category'),
			));
		}
		
		// Highlight the search keyword in the title and content if they match the keyword
		$referer   = $this->getRequest()->getServer('HTTP_REFERER');
		$searchUrl = $this->view->serverUrl() . $this->view->url(array(), 'content_article_search');
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
	
	////////// HELPER ACTIONS //////////
	
	/**
	 * Counts the number of articles by status
	 * 
	 * @return void
	 */
	public function countAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language'); 
		$counters = array(
			'total' => 0,
		);
		foreach (Content_Models_Article::$STATUS as $status) {
			$counters[$status] = Content_Services_Article::count(array(
									'status'   => $status,
									'language' => $language,
								));
			$counters['total'] += $counters[$status];
		}
		$this->_helper->json($counters);
	}
	
	/**
	 * Checks if the slug is taken
	 * 
	 * @return void
	 */
	public function slugAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$articleId = $request->getPost('article_id');
		$slug	   = $request->getPost('slug');
		
		$articles  = Content_Services_Article::find(array('slug' => $slug), 0, 1);
		$this->_helper->json(array(
			'available' => ($articles == null || count($articles) == 0 || $articles[0]->article_id == $articleId) ? true : false,
		));
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates or deactivates article
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$articleId = $request->getParam('article_id');
		$article   = Content_Services_Article::getById($articleId);
		if (!$article) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$article->status = $article->status == Content_Models_Article::STATUS_ACTIVATED
								? Content_Models_Article::STATUS_NOT_ACTIVATED
								: Content_Models_Article::STATUS_ACTIVATED;
			$result = Content_Services_Article::updateStatus($article);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}
	
	/**
	 * Adds new article
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$categories = $request->getPost('categories', array());
				$article	= new Content_Models_Article(array(
					'category_id'	   => $request->getPost('category'),
					'type'			   => $request->getPost('type', 'article'),
					'title'			   => $request->getPost('title'),
					'sub_title'		   => $request->getPost('sub_title'),
					'slug'			   => $request->getPost('slug'),
					'description'	   => $request->getPost('description'),
					'meta_description' => $request->getPost('meta_description'),
					'meta_keyword'	   => $request->getPost('meta_keyword'),
					'content'		   => $request->getPost('content'),
					'cover_title'	   => $request->getPost('cover_title'),
					'layout'		   => $request->getPost('layout'),
					'user_name'		   => Zend_Auth::getInstance()->getIdentity()->user_name,
					'author'		   => $request->getPost('author'),
					'credit'		   => $request->getPost('credit'),
					'featured'		   => $request->getPost('featured') ? 1 : 0,
					'image_icon'	   => $request->getPost('image_icon') ? 1 : 0,
					'video_icon'	   => $request->getPost('video_icon') ? 1 : 0,
					'created_user'	   => Zend_Auth::getInstance()->getIdentity()->user_id,
					'created_date'	   => date('Y-m-d H:i:s'),
					'status'		   => Content_Models_Article::STATUS_NOT_ACTIVATED,
					'language'		   => $request->getPost('language'),
					'translations'	   => $request->getPost('translations'),
				));
				
				if ($article->category_id != '' && !in_array($article->category_id, $categories)) {
					$categories[] = $article->category_id;
				}
				$article->categories = $categories;
				
				// Set images
				$thumbnails = $request->getPost('thumbnails');
				if ($thumbnails != '') {
					$thumbnails = Zend_Json::decode($thumbnails);
					foreach ($thumbnails as $size => $url) {
						$size = 'image_' . $size;
						$article->$size = $url;
					}
				}
				
				// Determine publishing date
				$publishingDate = $request->getPost('publishing_date');
				$publishingTime = $request->getPost('publishing_time');
				if ($publishingDate) {
					$article->publishing_date = $publishingDate . ' ' . substr($publishingTime, 1);
				}
				
				$articleId = Content_Services_Article::add($article);
				$article->article_id = $articleId;
				
				// Set tags
				if ($tagIds = $request->getPost('tags')) {
					$tags = array();
					foreach ($tagIds as $tagId) {
						$tags[] = Tag_Services_Tag::getById($tagId);
					}
					Content_Services_Article::setTags($article, $tags);
				}
				
				// Add revision
				$article  = Content_Services_Article::getById($articleId);
				$article->categories = $categories;
				$revision = new Content_Models_Revision(array(
					'is_active'		  => 1,
					'versioning_date' => $article->created_date,
				));
				$revision->setArticle($article);
				Content_Services_Revision::add($revision);
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
					'status' => $article->status,
				));
				break;
			default:
				$sourceId = $request->getParam('source_id');
				$source   = $sourceId ? Content_Services_Article::getById($sourceId) : null;
				
				$this->view->assign(array(
					'source'	=> $source,
					'language'	=> $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Updates article's cover
	 * 
	 * @return void
	 */
	public function coverAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$articleId  = $request->getParam('article_id');
		$thumbnails = $request->getParam('thumbnails');
		$thumbnails = Zend_Json::decode($thumbnails);
		
		$article = Content_Services_Article::getById($articleId);
		$result  = Content_Services_Article::updateCover($article, $thumbnails);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Deletes article
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$articleId = $request->getParam('article_id');
		
		$article = Content_Services_Article::getById($articleId);
		switch ($format) {
			case 'json':
				$status = $article ? $article->status : null;
				$result = Content_Services_Article::delete($article);
				$this->_helper->json(array(
										'result'	 => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
										'article_id' => $articleId,
										'status'	 => $status,
									));
				break;
			default:
				$this->view->assign('article', $article);
				break;
		}
	}
	
	/**
	 * Edits article
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$articleId = $request->getParam('article_id');
		
		switch ($format) {
			case 'json':
				$categories = $request->getPost('categories', array());
				$article	= Content_Services_Article::getById($articleId);
				
				$article->category_id	   = $request->getPost('category');
				$article->type			   = $request->getPost('type', 'article');
				$article->title			   = $request->getPost('title');
				$article->sub_title		   = $request->getPost('sub_title');
				$article->slug			   = $request->getPost('slug');
				$article->description	   = $request->getPost('description');
				$article->meta_description = $request->getPost('meta_description');
				$article->meta_keyword	   = $request->getPost('meta_keyword');
				$article->content		   = $request->getPost('content');
				$article->cover_title	   = $request->getPost('cover_title');
				$article->layout		   = $request->getPost('layout');
				$article->author		   = $request->getPost('author');
				$article->credit		   = $request->getPost('credit');
				$article->featured		   = $request->getPost('featured') ? 1 : 0;
				$article->image_icon	   = $request->getPost('image_icon') ? 1 : 0;
				$article->video_icon	   = $request->getPost('video_icon') ? 1 : 0;
				$article->updated_user	   = Zend_Auth::getInstance()->getIdentity()->user_id;
				$article->updated_date	   = date('Y-m-d H:i:s');
					
				if ($article->category_id != '' && !in_array($article->category_id, $categories)) {
					$categories[] = $article->category_id;
				}
				$article->categories = $categories;
				
				// Set images
				$thumbnails = $request->getPost('thumbnails');
				if ($thumbnails != '') {
					$thumbnails = Zend_Json::decode($thumbnails);
					foreach ($thumbnails as $size => $url) {
						$size = 'image_' . $size;
						$article->$size = $url;
					}
				}
				
				// Update translation
				$article->new_translations = $request->getPost('translations');
				if (!$article->new_translations) {
					$article->new_translations = Zend_Json::encode(array(
						$article->language => (string) $article->article_id,
					));
				}
				
				// Determine publishing date
				$publishingDate = $request->getPost('publishing_date');
				$publishingTime = $request->getPost('publishing_time');
				if ($publishingDate) {
					$article->publishing_date = $publishingDate . ' ' . substr($publishingTime, 1);
				}
				
				$result = Content_Services_Article::update($article);
				
				// Set tags
				if ($tagIds = $request->getPost('tags')) {
					$tags = array();
					foreach ($tagIds as $tagId) {
						$tags[] = Tag_Services_Tag::getById($tagId);
					}
					Content_Services_Article::setTags($article, $tags);
				}
				
				// Add revision
				$revision = new Content_Models_Revision(array(
					'comment'		  => $request->getPost('comment'),
					'is_active'		  => 1,
					'versioning_date' => $article->updated_date,
					'tags'			  => $tagIds,
				));
				$revision->setArticle($article);
				Content_Services_Revision::add($revision);
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$article = Content_Services_Article::getById($articleId);
				
				// Get the translations of the article
				$translations = null;
				if ($article) {
					$languages = Zend_Json::decode($article->translations);
					unset($languages[$article->language]);
					$translations = array();
					foreach ($languages as $locale => $id) {
						$translations[] = Content_Services_Article::getById($id);
					}
				}
				
				$this->view->assign(array(
					'article'	   => $article,
					'languages'	   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'translations' => $translations,
					'tags'		   => Content_Services_Article::getTags($article),
				));
				break;
		}
	}
	
	/**
	 * Empties the trash
	 * 
	 * @return void
	 */
	public function emptyAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$result = Content_Services_Article::emptyTrash();
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				break;
		}
	}
	
	/**
	 * Lists articles
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$q		  = $request->getParam('q');
		$format	  = $request->getParam('format');
		$default  = array(
			'page'		   => 1,
			'category_id'  => null,
			'status'	   => null,
			'keyword'	   => null,
			'per_page'	   => 20,
			'view_size'	   => 'thumbnail',
			'language'	   => Core_Services_Config::get('core', 'localization_default_language', 'en_US'),
			'sort_by'	   => 'ordering',
			'sort_dir'	   => 'ASC',
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$articles = Content_Services_Article::find($criteria, $offset, $criteria['per_page']);
		$total	  = Content_Services_Article::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($articles, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		switch ($format) {
			case 'json':
				// For other page/widget which want to get the list of articles
				$paginatorTopic = $request->getParam('topic', '/app/content/article/list/onGotoPage');
				$items = array();
				foreach ($articles as $article) {
					$properties			= $article->getProperties();
					$properties['link'] = $article->getViewUrl();
					$items[]			= $properties;
				}
				$data = array(
					'articles'	=> $items,
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('" . $paginatorTopic . "', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			case 'html':
			default:
				$this->view->assign(array(
					'articles'	=> $articles,
					'total'		=> $total,
					'criteria'	=> $criteria,
					'paginator' => $paginator,
				));
				break;
		}
	}
	
	/**
	 * Moves articles to other category
	 * 
	 * @return void
	 */
	public function moveAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$articleId	= $request->getParam('article_id');
		$categoryId = $request->getParam('category_id');
		
		$result = Content_Services_Article::move($articleId, $categoryId);
		$this->_helper->json(array(
			'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'article_id'  => $articleId,
			'category_id' => $categoryId,
		));
	}
	
	/**
	 * Saves order of articles
	 * 
	 * @return void
	 */
	public function orderAction()
	{
		$this->view->headTitle()->set($this->view->translator()->_('article.order.title'));
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$data	 = $request->getPost('data');
		$data	 = Zend_Json::decode($data);
		foreach ($data as $index => $item) {
			Content_Services_Article::updateOrder($item['article_id'], $item['category_id'], $item['index']);
		}
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
}
