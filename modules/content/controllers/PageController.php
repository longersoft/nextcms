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
 * @version		2012-05-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_PageController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Views page details
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
			$page = Content_Services_Article::getById($articleId);
		} elseif ($slug) {
			$result  = Content_Services_Article::find(array(
				'slug'	 => $slug,
				'type'	 => Content_Models_Article::TYPE_PAGE,
				'status' => Content_Models_Article::STATUS_ACTIVATED,
			), 0, 1);
			$page = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($page == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the page');
		}
		$request->setParam('entity_class', get_class($page))
				->setParam('entity_id', $page->article_id);
		
		// Set the meta tags
		$keywords = array();
		if ($page->meta_keyword) {
			$keywords[] = strip_tags($page->meta_keyword);
		}
		$tags = Content_Services_Article::getTags($page);
		if ($tags) {
			foreach ($tags as $tag) {
				$keywords[] = $tag->title;
			}
		}
		
		if (count($keywords) > 0) {
			$this->view->headMeta()->setName('keywords', implode(',', $keywords));
		}
		$this->view->headMeta()->setName('description', $page->meta_description ? strip_tags($page->meta_description) : $page->description);
		
		// Filter the article's title and content 
		$page->content = Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleContent', $page->content);
		$page->title   = Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', $page->title);
		
		$this->view->assign(array(
			'article' => $page,
			'tags'	  => $tags,
		));
		
		// Update the number of views
		Core_Services_Counter::register($page, 'views', 'Content_Services_Article::increaseNumViews', array($page));
	}
}
