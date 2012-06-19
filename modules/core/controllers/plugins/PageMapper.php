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
 * @version		2012-05-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Controllers_Plugins_PageMapper extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Return if I am in the back-end
		if (Zend_Layout::getMvcInstance() != null && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			return;
		}
		
		// Get current template
		$template = Core_Services_Template::getCurrentTemplate();
		
		$file = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'pages' . DS . 'pages.' . APP_ENV . '.php';
		if (!file_exists($file)) {
			return;
		}
		$pages = include_once $file;
		
		// Get current route
		$router = Zend_Controller_Front::getInstance()->getRouter();
		try {
			$route = $router->getCurrentRoute();
		} catch (Exception $e) {
			return;
		}
		
		// Get route's name
		$defaults  = ($route instanceof Zend_Controller_Router_Route_Chain) 
					? $route->match(Zend_Controller_Front::getInstance()->getRequest()) 
					: $route->getDefaults();
		$routeName = $defaults['module'] . '_' . $defaults['controller'] . '_' . $defaults['action'];
		$routeName = strtolower($routeName);
		
		// Get the current language
		$request  = Zend_Controller_Front::getInstance()->getRequest();
		$language = $request->getParam('lang');
		if (!$language) {
			$language = Core_Services_Config::get('core', 'language_code', 'en_US');
		}
		
		if (!isset($pages[$routeName][$language])) {
			return;
		}
		$data		= $pages[$routeName][$language];
		$pageUrl	= '';
		$encodedUrl = '';
		$url		= $request->getRequestUri();
		$baseUrl    = Zend_Controller_Front::getInstance()->getBaseUrl();
		
		$isHomepage = ($url == $baseUrl) || ($url == '/' && $baseUrl == '');
		if (!$isHomepage) {
			foreach ($data as $encodedUrl => $value) {
				if ($value['url'] && strpos($url, $value['url']) !== false) {
					$pageUrl = $value['url'];
					break;
				}
			}
		}
		
		$page = new Core_Models_Page(array(
			'template' => $template,
			'route'	   => $routeName,
			'language' => $language,
			'url'	   => $pageUrl,
		));
		
		// Set page title if it is set
		if (isset($data[$encodedUrl]['title']) && isset($data[$encodedUrl]['title'])) {
			$page->title = $data[$encodedUrl]['title'];
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$view->headTitle()->append($page->title);
		}
		
		// Set special parameters to use later
		$request->setParam('appCurrentPage', $page);
		if (isset($data[$encodedUrl]['cache_lifetime']) && (int) $data[$encodedUrl]['cache_lifetime'] > 0) {
			$cacheKey = md5(serialize($request->getParams()));
			
			if ($cache = Core_Services_Cache::getInstance()) {
				if (($fromCache = $cache->load($cacheKey))) {
					$request->setModuleName('core')
							->setControllerName('Cache')
							->setActionName('output')
							->setParam('appPageBody', $fromCache)
							->setDispatched(true);
				} else {
					$request->setParam('appSavePageToCache', true)
							->setParam('appPageCacheKey', $cacheKey)
							->setParam('appPageCacheLifetime', $data[$encodedUrl]['cache_lifetime']);
				}
			}
		}
	}
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown()
	{
		// Return if I am in the back-end
		if (Zend_Layout::getMvcInstance() != null && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			return;
		}
		
		$request = $this->getRequest();
		$page  	 = $request->getParam('appCurrentPage');
		$cache	 = Core_Services_Cache::getInstance();
		
		if ($page && $cache && $request->getParam('appSavePageToCache') === true) {
			$cacheKey = $request->getParam('appPageCacheKey');
			
			// Get response content
			$content = $this->getResponse()->getBody()
					. '<!-- cached at ' . date('Y-m-d H:i:s') . ' -->';
			
			// Save to cache
			$lifeTime = $request->getParam('appPageCacheLifetime');
			$cacheTag = md5(implode('_', array($page->template, $page->language, $page->route, $page->url)));
			$cache->save($content, $cacheKey, array('Page_' . $cacheTag, Core_Services_Cache::TAG_SITE_CONTENT), $lifeTime);
		}
	}
}
