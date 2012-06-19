<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_CacheController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Shows output from cache
	 * 
	 * @return void
	 */
	public function outputAction()
	{
		$request = $this->getRequest();
		$content = $request->getParam('appPageBody');
		
		Zend_Layout::getMvcInstance()->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		$this->getResponse()->setBody($content);
	}
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Cleans the cache
	 * 
	 * @return void
	 */
	public function cleanAction()
	{
		$request = $this->getRequest();
		$type	 = $request->getParam('type', 'data');
		switch ($type) {
			// Clean caching CSS files
			case 'css':
				$this->view->style()->cleanCaching();
				break;
			
			// Clean caching Javascript files
			case 'js':
				$this->view->script()->cleanCaching();
				break;
				
			// Clean caching data
			case 'data':
			default:
				$cache = Core_Services_Cache::getInstance();
				if ($cache) {
					$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array(Core_Services_Cache::TAG_SITE_CONTENT));
				}
				break;
		}
		
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Sets the cache lifetime
	 * 
	 * @return void
	 */
	public function pageAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$pageId   = $request->getParam('page_id');
		$lifetime = $request->getParam('lifetime');
		
		// Save the cache settings
		$page = Core_Services_Page::getById($pageId);
		$page->cache_lifetime = $lifetime;
		Core_Services_Page::update($page);
		
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Removes cache of given page
	 * 
	 * @return void
	 */
	public function removeAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$pageId   = $request->getParam('page_id');
		$page     = Core_Services_Page::getById($pageId);
		$result   = false;
		
		if ($page) {
			$result	  = true;
			
			// Build the cache tag
			// See Core_Controllers_Plugins_PageMapper::dispatchLoopShutdown()
			$cacheTag = md5(implode('_', array($page->template, $page->language, $page->route, $page->url)));
			$cache    = Core_Services_Cache::getInstance();
			if ($cache) {
				$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('Page_' . $cacheTag));
			}
		}
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
