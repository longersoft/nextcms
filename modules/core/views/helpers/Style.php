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
 * @version		2012-05-11
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_Style extends Zend_View_Helper_HeadLink
{
	/**
	 * The full URL of current processing CSS file
	 * 
	 * @var string
	 */
	private $_currentCssFile;
	
	/**
	 * Gets the view helper instance
	 * 
	 * @return Core_View_Helper_Style
	 */
	public function style()
	{
		return $this;
	}
	
	/**
	 * Minifies CSS
	 * 
	 * @return string
	 */
	public function minify()
	{
		Core_Services_Db::connect('slave');
		$compress = Core_Services_Config::get('core', 'compress_css', 'false') == 'true';
		if (!$compress) {
			return $this->toString();
		}
		
		require_once 'Minify/CSS.php';
		
		// Create cache instance
		$cache    = $this->_getCacheInstance();
		$request  = Zend_Controller_Front::getInstance()->getRequest();
		$cacheKey = array(
			'css',
			$request->getModuleName(),
			$request->getControllerName(),
			$request->getActionName(),
		);
		$cacheKey  = md5(implode('_', $cacheKey));
		$cacheData = $cache->load($cacheKey);
		
		if ($cacheData) {
			return $cacheData['content'];
		} else {
			// Remove the old file
			if (isset($cacheData['file']) && $cacheData['file']) {
				$oldFile = APP_TEMP_DIR . DS . 'css' . DS . $cacheData['file'];
				if (file_exists($oldFile)) {
					@unlink($oldFile);
				}
			}
			$cache->remove($cacheKey);
			
			$cacheData = array(
				'file'	  => null,
				'content' => '',
			);
			$iterator  = $this->getIterator();
			$files     = array();
			foreach ($iterator as $item) {
				if ($item->type == 'text/css') {
					$files[] = $item->href;
				} else {
					$cacheData['content'] .= $this->itemToString($item);
				}
			}
			
			// Push all external CSS files into one file
			if (count($files) > 0) {
				$rootUrl = $this->view->APP_ROOT_URL;
				$content = '';
				foreach ($files as $f) {
					if (substr($f, 0, 7) != 'http://') {
						$f = $rootUrl . '/' . ltrim($f);
					}
					
					$this->_currentCssFile = $f;
					$str	  = file_get_contents($f);
					$str	  = preg_replace_callback('/background([-image]*):[\s]*([#0-9a-zA-Z]*)[\s]*url\(["|\']*(.*)["|\']*\)([^;]*)/', array($this, '_processBackgroundImage'), $str);
					$content .= Minify_CSS::minify($str) . "\n";
				}
				$cacheData['file'] = $cacheKey  . time() . '.css';
				@file_put_contents(APP_TEMP_DIR . DS . 'css' . DS . $cacheData['file'], $content);
				$cacheData['content'] .= '<link type="text/css" rel="stylesheet" href="' . $rootUrl . '/temp/css/' . $cacheData['file'] . '" />';
			}
			
			// Save to cache
			$cache->save($cacheData, $cacheKey);
		}
		
		return $cacheData['content'];
	}
	
	/**
	 * Removes all caching CSS files
	 * 
	 * @return void
	 */
	public function cleanCaching()
	{
		$cache = $this->_getCacheInstance();
		$cache->clean();
	}
	
	/**
	 * Sets up to compress and cache CSS files
	 * 
	 * @return void
	 */
	public function setupCaching()
	{
		Core_Base_File::createDirectories('css', APP_TEMP_DIR);
		$file = APP_TEMP_DIR . DS . 'css' . DS . '.htaccess';
		if (!file_exists($file)) {
			@file_put_contents($file, 'Allow from all');
		}
	}
	
	/**
	 * Gets the cache instance
	 * 
	 * @return Zend_Cache_Core
	 */
	private function _getCacheInstance()
	{
		return Zend_Cache::factory('Core', 'File', array(
										'lifetime'				  => 604800,	// Cache in 7 days
										'automatic_serialization' => true,
										'cache_id_prefix'		  => 'app_css_',
									), array(
										'cache_dir' => APP_TEMP_DIR . DS . 'css',
									));
	}
	
	private function _processBackgroundImage($matches)
	{
		// $url is full URL of current processing file
		$url  = $this->_currentCssFile;
		
		$isBackgroundImage  = $matches[1];	// Can be "-image" or ""
		$backgroundColor    = $matches[2];	// Can be #hex or ""
		$backgroundFile     = $matches[3];
		$backgroundPosition = $matches[4];
		
		// $backgroundFile represents the path of background image. It can be one of the following formats:
		// - Relative path:
		//	 1) folder/sub-folder/image
		//	 2) ../folder/image
		//	 ../../folder/image
		// - Full path
		//	 3) http://domain/folder/sub-folder/image
		$backgroundFile = ltrim($backgroundFile, '/');

		$file = '';
		switch (true) {
			// Case 3
			case ('http://' == substr($backgroundFile, 0, 7)):
			case ('https://' == substr($backgroundFile, 0, 8)):
				$file = $backgroundFile;
				break;
			
			// Case 2
			case ('../' == substr($backgroundFile, 0, 3)):
				$path	= pathinfo($url);
				$url    = substr($url, 0, -strlen($path['basename']));
				$url	= rtrim($url, '/');
				$paths  = explode('/', $url);
				while ('../' == substr($backgroundFile, 0, 3)) {
					$backgroundFile = substr($backgroundFile, 3);
					array_pop($paths);
				}
				$file = implode('/', $paths);
				$file = rtrim($file, '/') . '/' . ltrim($backgroundFile, '/');
				break;
				
			// Case 1
			default:
				$path = pathinfo($url);
				$url  = substr($url, 0, -strlen($path['basename']));
				$file = rtrim($url, '/') . '/' . $backgroundFile;
				break;
		}
		
		$prop = 'background' . $isBackgroundImage . ':' . $backgroundColor . ' url(' . $file . ')' . $backgroundPosition;
		return $prop;
	}
}
