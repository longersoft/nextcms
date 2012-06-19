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

class Core_View_Helper_LayoutContent extends Zend_View_Helper_Abstract
{
	/**
	 * Shows the content of layout
	 * 
	 * @param array $data Contains the following properties:
	 * - cache: Cache settings
	 * - filters: Array of filters. Each item is the name of filter class
	 * @return string
	 */
	public function layoutContent($data = array())
	{
		$caching  = false;
		$cacheKey = null;
		$cache	  = null;
		
		if (isset($data['cache']['lifetime']) && !empty($data['cache']['lifetime'])) {
			// Get the cache instance
			$cache = Core_Services_Cache::getInstance();
			if ($cache) {
				$params	  = Zend_Controller_Front::getInstance()->getRequest()->getParams();
				$cacheKey = md5(serialize($params));
				
				if (($fromCache = $cache->load($cacheKey))) {
					return $fromCache;
				} else {
					$caching = true;
				}
			}
		}
		
		$output = $this->view->layout()->content;
		
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
		
		// Save to cache if caching is enabled
		if ($cache && $caching && $cacheKey) {
			$cache->save($output, $cacheKey, array('LayoutContent'), $data['cache']['lifetime']);
		}
		
		return $output;
	}
}
