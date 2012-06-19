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
 * @subpackage	views
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Renders widget
 */
class Core_View_Helper_Widget
{
	/**
	 * Stores the request params
	 * 
	 * @var array
	 */
	private $_reqParams;
	
	/**
	 * Array of installed modules
	 * 
	 * @var array
	 */
	private $_modules = null;
	
	/**
	 * Renders a widget
	 * 
	 * @param string $name Name of widget
	 * @param string $module Name of module
	 * @param array $data Contains the following properties:
	 * - params: Array of widget's parameters
	 * - filters: Array of filters. Each item is the name of filter class
	 * @return string
	 */
	public function widget($name, $module, $data = array())
	{
		if ($this->_modules == null) {
			$this->_modules = Core_Services_Module::getBootstrapModules();
		}
		// Return if the module has not been installed yet
		if ($this->_modules && !in_array(strtolower($module), $this->_modules)) {
			return '';
		}
		
		$class = ucfirst(strtolower($module)) . '_Widgets_' . ucfirst(strtolower($name)) . '_Widget';
		if ($class == '' || !class_exists($class)) {
			return '';
		}
		
		$widget   = new $class($name, $module);
		$caching  = false;
		$cacheKey = null;
		$cache	  = null;
		
		if (isset($data['cache']['lifetime']) && !empty($data['cache']['lifetime'])) {
			// Get the cache instance
			$cache = Core_Services_Cache::getInstance();
			if ($cache) {
				$cacheKey = $class . '_' . md5($module . '_' . $name . '_' . serialize($data['params']));
				
				if (($fromCache = $cache->load($cacheKey))) {
					return $fromCache;
				} else {
					$caching = true;
				}
			}
		}
		
		// Define the request parameters
		$requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$widgetParams  = array();
		if (isset($data['params']) && is_array($data['params'])) {
			foreach ($data['params'] as $k => $v) {
				if ($v == '__AUTO__' && isset($requestParams[$k])) {
					$widgetParams[$k] = $requestParams[$k];
				} else {
					$widgetParams[$k] = $v;
				}
			}
		}
		
		// Render the widget
		$output = $widget->show($widgetParams);
		
		if (isset($data['filters']) && count($data['filters']) > 0) {
			// Filter the output
			foreach ($data['filters'] as $index => $class) {
				$class = Core_Base_Clazz::normalizeClass($class);
				if (class_exists($class)) {
					$filterObject = Core_Services_Hook::getRegisteredInstance($class);
					if ($filterObject == null) {
						$filterObject = new $class;
					}
					if ($filterObject instanceof Zend_Filter_Interface) {
						$output = $filterObject->filter($output);
					}
				}
			}
		}
		
		// Save to cache
		if ($cache && $caching && $cacheKey) {
			$cache->save($output, $cacheKey, array($module . '_Widgets', Core_Services_Cache::TAG_SITE_CONTENT), $data['cache']['lifetime']);
		}
		
		return $output;
	}
}
