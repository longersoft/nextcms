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
 * @version		2012-05-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * This view helper is used to include inline scripts.
 * The HeadScript view helper can capture the inline scripts by using
 * the captureStart() and captureEnd() methods:
 * 
 * <?php $this->headScript()->captureStart(); ?>
 * ... inline scripts ...
 * <?php $this->headScript()->captureEnd(); ?>
 * 
 * The following method shows all the inline scripts:
 * <?php echo $this->headScript(); ?>
 * 
 * But with this way, any inline script of the widget will be shown many times.
 * This Script helper helps us show widget's inline script only once time in page.
 * Use the following sample code in widget script:
 * 
 * <?php $this->script()->captureStartOnce(X); ?>
 * ... inline scripts ...
 * <?php $this->script()->captureEndOnce(X); ?>
 * 
 * where X is a string to identify the script with other inline scripts
 * (Combination of the module's name, widget's name and widget's template is good one)
 * 
 * Then it is possible to show all the inline scripts by using:
 * <?php echo $this->script(); ?>
 */
class Core_View_Helper_Script extends Zend_View_Helper_HeadScript
{
	/**
	 * Array of inline scripts that are captured only once in a page
	 * 
	 * @var array
	 */
	private $_inlineCapturedOnceScripts = array();
	
	/**
	 * Gets the view helper instance
	 * 
	 * @return Core_View_Helper_Script
	 */
	public function script()
	{
		return $this;
	}
	
	/**
	 * @see Zend_View_Helper_Placeholder_Container_Standalone::__toString()
	 */
	public function __toString()
	{
//		$output = parent::__toString();
		$output = $this->toString();
		$this->reset();
		return $output;
	}
	
	/**
	 * @param string $name
	 * @param bool $staticContent If the content of inline script is static
	 * and does not depend on the request, and you want to put it to the caching file,
	 * then set it as TRUE
	 * @return void
	 */
	public function captureStartOnce($name, $staticContent = false)
	{
		ob_start();
	}
	
	/**
	 * @param string $name
	 * @param bool $staticContent If the content of inline script is static
	 * and does not depend on the request, and you want to put it to the caching file,
	 * then set it as TRUE
	 * @return void
	 */
	public function captureEndOnce($name, $staticContent = false)
	{
		$content = ob_get_clean();
		
		if (!isset($this->_inlineCapturedOnceScripts[$name])) {
			$this->_inlineCapturedOnceScripts[$name] = $content;
			
			if (!$this->offsetExists($name)) {
				$this->offsetSetScript($name, $content, 'text/javascript', array(
					'isStaticContent' => $staticContent,
				));
			}
		}
	}
	
	/**
	 * Minifies all scripts
	 * 
	 * @param string $cacheKey The cache key. If it is not defined, it will be genarated
	 * based on the current request
	 * @param bool $reset If TRUE, all the file and inline scripts registered
	 * with this view helper will be reset to empty
	 * @return string
	 */
	public function minify($cacheKey = null, $reset = true)
	{
		Core_Services_Db::connect('slave');
		$compress = Core_Services_Config::get('core', 'compress_js', 'false') == 'true';
		if (!$compress) {
			$content = $this->toString();
			if ($reset) {
				$this->reset();
			}
			return $content;
		}
		
		require_once 'JSMin.php';
		
		// Create cache instance
		$cache    = $this->_getCacheInstance();
		$request  = Zend_Controller_Front::getInstance()->getRequest();
		if ($cacheKey == null) {
			$cacheKey = array(
				'js',
				$request->getModuleName(),
				$request->getControllerName(),
				$request->getActionName(),
			);
			$cacheKey  = md5(implode('_', $cacheKey));
		}
		$cacheData = $cache->load($cacheKey);
		
		// If found in cache
		$iterator	   = $this->getIterator();
		$inlineScripts = array();
		if ($cacheData) {
			foreach ($iterator as $item) {
				if ($item->source && (!isset($item->attributes['isStaticContent']) || $item->attributes['isStaticContent'] == false)) {
					$inlineScripts[] = JSMin::minify($item->source);
				}
			}
		} else {
			// Remove the old file
			if (isset($cacheData['file']) && $cacheData['file']) {
				$oldFile = APP_TEMP_DIR . DS . 'js' . DS . $cacheData['file'];
				if (file_exists($oldFile)) {
					@unlink($oldFile);
				}
			}
			$cache->remove($cacheKey);
			
			$cacheData = array(
				'file'	  => null,
				'content' => '',
			);
			$rootUrl	   = $this->view->APP_ROOT_URL;
			$fileScript	   = '';
			$cachedFileUrl = null;
			foreach ($iterator as $item) {
				if ($item->source == null) {
					// File script
					$f = $item->attributes['src'];
					if (substr($f, 0, 7) != 'http://') {
						$f = $rootUrl . '/' . ltrim($f);
					}
					$fileScript .= JSMin::minify(file_get_contents($f)) . "\n";
					if ($cachedFileUrl == null) {
						$cacheData['file']	   = $cacheKey  . time() . '.js';
						$cachedFileUrl		   = $rootUrl . '/temp/js/' . $cacheData['file'];
						$cacheData['content'] .= '<script type="text/javascript" src="' . $cachedFileUrl . '"></script>';
					}
				} else {
					// Inline script
					$s = JSMin::minify($item->source);
					
					if (isset($item->attributes['isStaticContent']) && $item->attributes['isStaticContent'] == true) {
						// The content of inline script is static, it does not depened on the URL
						// I can put its content to cached file
						$fileScript .= $s;
					} else {
						$inlineScripts[] = $s;
					}
				}
			}
			
			if ($fileScript) {
				@file_put_contents(APP_TEMP_DIR . DS . 'js' . DS . $cacheData['file'], $fileScript);
			}
			
			// Save to cache
			$cache->save($cacheData, $cacheKey);
		}
		
		// Reset all scripts
		if ($reset) {
			$this->reset();
		}
		return $cacheData['content'] . '<script type="text/javascript">' . implode('', $inlineScripts) . '</script>';
	}
	
	/**
	 * Removes all caching Javascript files
	 * 
	 * @return void
	 */
	public function cleanCaching()
	{
		$cache = $this->_getCacheInstance();
		$cache->clean();
	}
	
	/**
	 * Sets up to compress and cache Javascript files and inline scripts
	 * 
	 * @return void
	 */
	public function setupCaching()
	{
		Core_Base_File::createDirectories('js', APP_TEMP_DIR);
		$file = APP_TEMP_DIR . DS . 'js' . DS . '.htaccess';
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
										'cache_id_prefix'		  => 'app_js_',
									), array(
										'cache_dir' => APP_TEMP_DIR . DS . 'js',
									));
	}
	
	/**
	 * Resets all scripts
	 * 
	 * @return void
	 */
	public function reset()
	{
		$this->_inlineCapturedOnceScripts = array();
		$this->getContainer()->exchangeArray(array());
	}
	
	/**
	 * @see Zend_View_Helper_HeadScript::toString()
	 */
	public function toString($indent = null)
	{
		$indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();

		if ($this->view) {
			$useCdata = $this->view->doctype()->isXhtml() ? true : false;
		} else {
			$useCdata = $this->useCdata ? true : false;
		}
		$escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
		$escapeEnd   = ($useCdata) ? '//]]>'       : '//-->';

		$items = array();
		// Don't sort the array of scripts
		// $this->getContainer()->ksort();

		foreach ($this as $item) {
			if (!$this->_isValid($item)) {
				continue;
			}
			$items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
		}

		$return = implode($this->getSeparator(), $items);
		return $return;
	}
}
