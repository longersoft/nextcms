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
 * @subpackage	tasks
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Tasks_Feedimporter_Task extends Core_Base_Extension_Task
{
	/**
	 * @see Core_Base_Extension_Task::execute()
	 */
	public function execute($params = null)
	{
		Core_Services_Db::connect('master');
		
		$feeds = Core_Services_Task::getOptionsByInstance($this);
		if (!$feeds || (count($feeds) == 0)) {
			return;
		}
		$conn = Core_Services_Db::getConnection();
		$dao  = Core_Services_Dao::factory(array(
									'module' => 'content',
									'task'   => 'feedimporter',
									'name'   => 'Entry',
							     ))
							     ->setDbConnection($conn);
		
		foreach ($feeds as $feed) {
			$url	  = $feed['url'];
			$language = $feed['language'];
			$category = $feed['category'];
			
			// Get the feed entries
			$entries = Zend_Feed_Reader::import($url);
			foreach ($entries as $entry) {
				$link = $entry->getLink();
				
				if ($dao->exist($link)) {
					continue;
				}
				
				// Get the author's names
				$authors	 = $entry->getAuthors();
				$authorNames = '';
				if ($authors && count($authors) > 0) {
					$authorNames = array();
					foreach ($authors as $author) {
						$authorNames[] = $author['name'];
					}
					$authorNames = implode(', ', $authorNames);
				}
				
				// Generate the slug
				$title = $entry->getTitle();
				$slug  = Core_Base_String::clean($title, '-', $language);
				
				// FIXME: Get article's image
//				$image = $entry->getImage();
//				$image = ($image && isset($image['uri'])) ? $image['uri'] : null;
				$image = null;
				
				// Get description and content
				$description = $entry->getDescription();
				$description = ($description == null || empty($description)) ? '' : $description; 
				$content	 = $entry->getContent();
				$content	 = ($content == null || empty($content)) ? $description : $content;
				
				// Get modified date
				$modifiedDate = $entry->getDateModified();
				if ($modifiedDate instanceof Zend_Date) {
					// See http://framework.zend.com/manual/en/zend.date.constants.html#zend.date.constants.selfdefinedformats
					$modifiedDate = $modifiedDate->toString('YYYY-MM-dd HH:mm:s');
				} else {
					$modifiedDate = date('Y-m-d H:i:s');
				}
				
				$article = new Content_Models_Article(array(
					'category_id'	   => $category,
					'categories'	   => array($category),
					'type'			   => Content_Models_Article::TYPE_ARTICLE,
					'title'			   => $title,
					'slug'			   => $slug,
					'description'	   => $description,
					'meta_description' => null,
					'meta_keyword'	   => null,
					'content'		   => $content,
					'layout'		   => '{"containers":[{"containers":[{"containers":[],"cls":"core.js.views.LayoutPortlet","zoneIndex":0,"widget":{"module":"content","name":"editor","title":"Editor","params":{"content":"' . addslashes($content) . '"}}}],"cls":"dojox.layout.GridContainer","numZones":1}],"cls":"dijit.layout.BorderContainer","region":"center","style":"height: 100%; width: 100%"}',
					'author'		   => $authorNames,
					'credit'		   => $link,
					'featured'		   => 0,
					'image_icon'	   => 0,
					'video_icon'	   => 0,
					'image_square'	   => $image,
					'image_thumbnail'  => $image,
					'image_small'	   => $image,
					'image_crop'	   => $image,
					'image_medium'	   => $image,
					'image_large'	   => $image,
					'image_original'   => $image,
					'cover_title'	   => null,
					'created_user'	   => null,
					'created_date'	   => $modifiedDate,
					'status'		   => Content_Models_Article::STATUS_NOT_ACTIVATED,
					'language'		   => $language,
				));
				
				// Add article
				$articleId = Content_Services_Article::add($article);
				
				// Add revision
				$article  = Content_Services_Article::getById($articleId);
				$revision = new Content_Models_Revision(array(
					'is_active'		  => 1,
					'versioning_date' => $article->created_date,
				));
				$revision->setArticle($article);
				Content_Services_Revision::add($revision);
				
				// Add entry
				$dao->add(new Content_Tasks_Feedimporter_Models_Entry(array(
					'feed_url'	   => $url,
					'link'		   => $link,
					'article_id'   => $articleId,
					'created_date' => date('Y-m-d H:i:s'),
				)));
			}
		}
	}
	
	/**
	 * Retrives the list of categories based on the selected language
	 * 
	 * @return void
	 */
	public function categoryAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$language   = $request->getParam('language');
		$categories = Category_Services_Category::getTree('content', $language);
		
		$items = array();
		foreach ($categories as $category) {
			$items[] = array(
				'category_id' => $category->category_id,
				'name'		  => $category->name,
				'depth'		  => $category->depth,
			);
		}
		echo Zend_Json::encode($items);
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$language = Core_Services_Config::get('core', 'localization_default_language', 'en_US');
		
		$this->view->assign(array(
			'language'	 => $language,
			'languages'  => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
			'categories' => Category_Services_Category::getTree('content', $language),
			'feeds'		 => Core_Services_Task::getOptions('feedimporter', 'content'),
		));
	}
	
	/**
	 * Saves the settings
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$urls		= $request->getParam('urls');
		$languages	= $request->getParam('languages');
		$categories = $request->getParam('categories');
		
		if (!$urls || count($urls) == 0) {
			return 'false';
		}
		
		$options = array();
		foreach ($urls as $index => $url) {
			$options[] = array(
				'url'	   => $url,
				'language' => $languages[$index],
				'category' => $categories[$index],
			);
		}
		
		$result = Core_Services_Task::setOptionsForInstance($this, $options);
		return $result ? 'true' : 'false';
	}
}
