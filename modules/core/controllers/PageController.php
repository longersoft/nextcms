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
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_PageController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Add new page
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		// Get the current template
		$currentTemplate = Core_Services_Config::get('core', 'template', 'default');
		$template		 = $request->getParam('template', $currentTemplate);
		
		switch ($format) {
			case 'json':
				$page = new Core_Models_Page(array(
					'name'		   => $request->getPost('name'),
					'title'		   => $request->getPost('title'),
					'route'		   => $request->getPost('route'),
					'url'		   => $request->getPost('url'),
					'ordering'	   => 0,
					'template'	   => $request->getPost('template', $currentTemplate),
					'language'	   => $request->getPost('language'),
					'translations' => $request->getPost('translations'),
				));
				
				Core_Services_Page::add($page);
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$sourceId = $request->getParam('source_id');
				$source	  = $sourceId ? Core_Services_Page::getById($sourceId) : null;
				$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
				
				// Group the front-end routes by module
				$frontendRoutes = Core_Services_Page::getFrontendRoutes($language);
				$routes			= array();
				if ($frontendRoutes) {
					foreach ($frontendRoutes as $name => $details) {
						$routes[$details['module']][$details['name']] = $details['description'];
					}
				}
				ksort($routes);
				$t = $routes;
				foreach ($t as $module => $r) {
					asort($r);
					$routes[$module] = $r;
				}
				
				$this->view->assign(array(
					'routes'	=> $routes,
					'source'	=> $source,
					'template'  => $template,
					'templates' => Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'templates'),
					'language'	=> $language,
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Deletes page
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$pageId	 = $request->getParam('page_id');
		$page	 = Core_Services_Page::getById($pageId);
		
		switch ($format) {
			case 'json':
				$result = Core_Services_Page::delete($page);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('page', $page);
				break;
		}
	}
	
	/**
	 * Edits page
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$pageId	 = $request->getParam('page_id');
		$page	 = Core_Services_Page::getById($pageId);
		
		switch ($format) {
			case 'json':
				$result = false;
				if ($page) {
					$page->name	 = $request->getPost('name');
					$page->title = $request->getPost('title');
					$page->route = $request->getPost('route');
					$page->url   = $request->getPost('url');
					
					// Update translation
					$page->new_translations = $request->getPost('translations');
					if (!$page->new_translations) {
						$page->new_translations = Zend_Json::encode(array(
							$page->language => (string) $page->page_id,
						));
					}
					
					$result = Core_Services_Page::update($page);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				// Get the translations of the menu
				$translations = null;
				if ($page) {
					$languages = Zend_Json::decode($page->translations);
					unset($languages[$page->language]);
					$translations = array();
					foreach ($languages as $locale => $id) {
						$translations[] = Core_Services_Page::getById($id);
					}
				}
				
				$language = $page ? $page->language : Core_Services_Config::get('core', 'localization_default_language', 'en_US');
				
				// Group the front-end routes by module
				$frontendRoutes = Core_Services_Page::getFrontendRoutes($language);
				$routes			= array();
				if ($frontendRoutes) {
					foreach ($frontendRoutes as $name => $details) {
						$routes[$details['module']][$details['name']] = $details['description'];
					}
				}
				ksort($routes);
				$t = $routes;
				foreach ($t as $module => $r) {
					asort($r);
					$routes[$module] = $r;
				}
				
				$this->view->assign(array(
					'page'		   => $page,
					'pageId'	   => $pageId,
					'routes'	   => $routes,
					'templates'	   => Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'templates'),
					'languages'	   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'translations' => $translations,
				));
				break;
		}
	}
	
	/**
	 * Exports layout of page
	 * 
	 * @return void
	 */
	public function exportAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$pageId  = $request->getParam('page_id');
		$page	 = Core_Services_Page::getById($pageId);
		
		if ($page && $page->layout) {
			$file    = Core_Services_PageLayout::buildXmlLayoutPath($page);
			$layout	 = Zend_Json::decode($page->layout);
			$content = Core_Services_PageLayout::buildXmlLayout($layout);
			file_put_contents($file, $content);
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
		}
		
		exit();
	}
	
	/**
	 * Sets filters to widget
	 * 
	 * @return void
	 */
	public function filterAction()
	{
		Core_Services_Db::connect('master');
		
		$request	   = $this->getRequest();
		$filterClasses = $request->getParam('filters', '');
		$filterClasses = $filterClasses ? Zend_Json::decode(strtolower($filterClasses)) : array();
		
		$availableFilters  = array();
		$additionalFilters = array();
		$installedFilters  = Core_Services_Hook::getInstalledHooks(array(
			'filter' => 1,
		));
		$filters = array();
		foreach ($installedFilters as $filter) {
			$clazz = ucfirst(strtolower($filter->module)) . '_Hooks_' . ucfirst(strtolower($filter->name)) . '_Hook';
			$clazz = strtolower($clazz);
			$filters[$clazz] = $filter;
		}
		
		// Define the classes of additional filters
		foreach ($filterClasses as $index => $clazz) {
			if (isset($filters[$clazz])) {
				$filter		   = $filters[$clazz];
				$filter->clazz = strtolower($clazz);
				$filter->used  = true;
				
				$availableFilters[] = $filter;
				unset($filters[$clazz]);
			} else {
				$additionalFilters[] = $clazz;
				unset($filterClasses[$index]);
			}
		}
		
		foreach ($filters as $clazz => $filter) {
			$filter->clazz = $clazz;
			$filter->used  = false;
			$availableFilters[] = $filter;
		}
		
		$this->view->assign(array(
			'container_id' => $request->getParam('container_id'),
			'filters'      => $availableFilters,
			'classes'	   => implode(',', $additionalFilters),
		));
	}
	
	/**
	 * Imports layout of page
	 * 
	 * @return void
	 */
	public function importAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$pageId  = $request->getParam('page_id');
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$page		  = Core_Services_Page::getById($pageId);
				$uploadedFile = $_FILES['uploadedfiles'];
				
				// Move the uploaded file
				$layoutFile = Core_Services_PageLayout::buildXmlLayoutPath($page);
				move_uploaded_file($uploadedFile['tmp_name'][0], $layoutFile);
				
				// Import the layout
				Core_Services_Page::importXmlLayout($page, $layoutFile);
				
				$result	  = array();
				$result[] = array(
					'path' => '',
				);
				
				// Clean the CSS, JS caching on the front-end
				$this->view->style()->cleanCaching();
				$this->view->script()->cleanCaching();
				
				// Returns the array in JSON format 
				// that will be processed by handler of onComplete() event of Dojo Uploader widget
				$this->_helper->json($result);
				break;
			default:
				$this->view->assign('page_id', $pageId);
				break;
		}
	}
	
	/**
	 * Layouts the page
	 * 
	 * @return void
	 */
	public function layoutAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$pageId	 = $request->getParam('page_id');
		$page	 = Core_Services_Page::getById($pageId);
		
		switch ($format) {
			case 'json':
				$page->layout = $request->getParam('layout');
				$result = Core_Services_Page::updateLayout($page);
				
				// Clean the CSS, JS caching on the front-end
				$this->view->style()->cleanCaching();
				$this->view->script()->cleanCaching();
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('page', $page);
				break;
		}
	}
	
	/**
	 * Lists pages
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		$q		 = $request->getParam('q');
		$default = array(
			'template' => null,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$pages	  = Core_Services_Page::find($criteria);
		$total	  = Core_Services_Page::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($pages, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		switch ($format) {
			case 'json':
				// Build data for the grid
				$paginatorTopic = $request->getParam('topic', '/app/core/page/list/onGotoPage');
				
				$items = array();
				foreach ($pages as $page) {
					$items[] = $page->getProperties();
				}
				$data = array(
					'pages'		=> array(
						'identifier' => 'page_id',
						'items'		 => $items,
					),
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('" . $paginatorTopic . "', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			default:
				// Get the list of templates
				$templates = array();
				
				$dirs = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'templates');
				foreach ($dirs as $dir) {
					if ($dir != 'admin') {
						$templates[] = $dir;
					}
				}
				
				$this->view->assign(array(
					'templates' => Zend_Json::encode($templates),
					'criteria'  => $criteria,
				));
				break;
		}
	}
	
	/**
	 * Orders the pages
	 * 
	 * @return void
	 */
	public function orderAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$pages   = $request->getParam('pages');
		$result  = false;
		if ($pages) {
			$result = true;
			$pages  = explode(',', $pages);
			foreach ($pages as $index => $pageId) {
				$page = Core_Services_Page::getById($pageId);
				$page->ordering = $index;
				$result = $result && Core_Services_Page::update($page);
			}
		}
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Sets control's properties
	 * 
	 * @return void
	 */
	public function propertyAction()
	{
		$default	= array(
			'title'			 => '',
			'id'			 => '',
			'css_class'		 => '',
			'css_style'		 => '',
			'cache_lifetime' => '',
		);
		
		$request	= $this->getRequest();
		$properties = $request->getParam('properties');
		$properties = $properties ? Zend_Json::decode($properties) : $default;
		$properties = array_merge($default, $properties);
		
		$this->view->assign(array(
			'container_id' => $request->getParam('container_id'),
			'cls'		   => $request->getParam('cls'),
			'properties'   => $properties,
		));
	}
}
