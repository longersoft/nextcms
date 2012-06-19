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
 * @subpackage	services
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Page
{
	/**
	 * Adds new page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return string Id of newly created page
	 */
	public static function add($page)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		$conn   = Core_Services_Db::getConnection();
		$pageId = Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'Page',
								   ))
								   ->setDbConnection($conn)
								   ->add($page);
		
		// Generate mapping file
		self::generateMappingFile($page->template);
		
		return $pageId;
	}
	
	/**
	 * Gets the number of pages by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Page',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return bool
	 */
	public static function delete($page)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Page',
						 ))
						 ->setDbConnection($conn)
						 ->delete($page);
		
		// Generate mapping file
		self::generateMappingFile($page->template);
		
		return true;
	}
	
	/**
	 * Deletes all pages belong to given template
	 * 
	 * @param string $template The template name
	 * @return bool
	 */
	public static function deleteByTemplate($template)
	{
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Page',
						 ))
						 ->setDbConnection($conn)
						 ->deleteByTemplate($template);
		return true;
	}
	
	/**
	 * Finds pages by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Page',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Generates a PHP file that map the current route with associated layout file
	 * 
	 * @param string $template The name of template
	 * @return void
	 */
	public static function generateMappingFile($template)
	{
		$pages = self::find(array(
			'template' => $template,
			'sort_by'  => 'ordering',
			'sort_dir' => 'ASC',
		));
		$data  = array();
		if ($pages) {
			foreach ($pages as $page) {
				$item = array(
					'url' => $page->url,
				);
				if ($page->title) {
					$item['title'] = $page->title;
				}
				if ((int) $page->cache_lifetime > 0) {
					$item['cache_lifetime'] = (int) $page->cache_lifetime;
				}
				$data[$page->route][$page->language][$page->url ? md5($page->url) : ''] = $item;
			}
		}
		
		$file   = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'pages' . DS . 'pages.' . APP_ENV . '.php';
		$writer = new Core_Base_Config_Writer_Php();
		$writer->setArrayName('pages');
		$writer->write($file, new Zend_Config($data));
	}
	
	/**
	 * Gets page instance by given Id
	 * 
	 * @param string $pageId Page's Id
	 * @return Core_Models_Error|null
	 */
	public static function getById($pageId)
	{
		if ($pageId == null || empty($pageId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Page',
								))
								->setDbConnection($conn)
								->getById($pageId);
	}
	
	/**
	 * Gets the list of front-end routes
	 * 
	 * @param string $language The language
	 * @return array
	 */
	public static function getFrontendRoutes($language)
	{
		$frontendRoutes = array();
		
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$routes = $router->getRoutes();
		
		$view   = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->translator()->setLanguage($language);
		
		foreach ($routes as $name => $route) {
			if ($route instanceof Zend_Controller_Router_Route_Chain) {
				continue;
			}
			$defaults = $route->getDefaults();
			if (isset($defaults['frontend']['enabled']) && $defaults['frontend']['enabled']) {
				$module = $defaults['module'];
				
				$frontendRoutes[$name] = array(
					'available'	  => true,
					'name'		  => $name,
					'description' => $view->translator()->setLanguageDir('/modules/' . $module . '/languages')->_($defaults['frontend']['translationKey']),
					'module'	  => $module,
				);
			}
		}
		
		// Reset the language dir
		$view->translator()->setLanguage(null)->setLanguageDir(null);
		
		return $frontendRoutes;
	}
	
	/**
	 * Updates given page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return bool
	 */
	public static function update($page)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Page',
						 ))
						 ->setDbConnection($conn)
						 ->update($page);
						 
		// Generate mapping file
		self::generateMappingFile($page->template);
		
		return true;
	}
	
	/**
	 * Updates the layout of page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return bool
	 */
	public static function updateLayout($page)
	{
		// Update the DB
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Page',
						 ))
						 ->setDbConnection($conn)
						 ->updateLayout($page);
		
		if (!$page->layout) {
			return false;
		}
		
		// Generate the layout file associating with the page
		$file	 = Core_Services_PageLayout::buildPhpLayoutPath($page);
		$layout  = Zend_Json::decode($page->layout);
		$content = Core_Services_PageLayout::buildPhpLayout($layout);
		
		@file_put_contents($file, $content);
		return true;
	}
	
	/**
	 * Imports layout from a XML file
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @param string $layoutFile The XML file that defines the layout of page
	 * @return bool
	 */
	public static function importXmlLayout($page, $layoutFile)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		
		// Parse the layout from XML file
		$layout = Core_Services_PageLayout::loadXmlLayout($layoutFile);
		
		// Update the layout
		$page->layout = Zend_Json::encode($layout);
		return self::updateLayout($page);
	}
}
