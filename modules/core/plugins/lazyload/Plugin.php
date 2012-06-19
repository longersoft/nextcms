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
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-05-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Lazyload_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * Indicate the lazy loading is enabled or not
	 * 
	 * @var bool
	 */
	private $_lazyLoading = false;
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::postDispatch()
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Return if the request is an Ajax one
		if (Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
			$this->_lazyLoading = false;
			return;
		}
		// or in the back-end
		if (!Zend_Layout::getMvcInstance() || 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->_lazyLoading = false;
			return;
		}
		// or the response returns an XML output (such as XML Feed)
		if ($headers = $this->getResponse()->getHeaders()) {
			foreach ($headers as $k => $v) {
				if ('Content-Type' == $v['name'] && strpos($v['value'], 'application/rss+xml;') !== false) {
					$this->_lazyLoading = false;
					return;
				}
			}
		}
		
		// Enable lazy loading mode
		$this->_lazyLoading = true;
		
		// Append lazy loading scripts
		$script = <<<END
$(document).ready(function() {
	$("img").lazyload();
});
END;
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->script()->appendFile($view->APP_STATIC_URL . '/modules/core/plugins/lazyload/jquery.lazyload.js');
		$view->script()->appendScript($script);
	}
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown()
	{
		if (!$this->_lazyLoading) {
			return;
		}
		
		$response = Zend_Controller_Front::getInstance()->getResponse();
		$body	  = $response->getBody();
		$doc	  = new DOMDocument();
		@$doc->loadHTML($body);
		
		// Find all img nodes
		$imgNodes = $doc->getElementsByTagName('img');
		if ($imgNodes) {
			$view			  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$placeHolderImage = $view->APP_STATIC_URL . '/modules/core/plugins/lazyload/images/1.gif';
			
			foreach ($imgNodes as $imgNode) {
				// Clone the img node
				$node = $imgNode->cloneNode();
				$src  = $imgNode->getAttribute('src');
				$node->setAttribute('src', $placeHolderImage);
				$node->setAttribute('data-original', $src);
				
				// Replace the old img node with new one
				$imgNode->parentNode->replaceChild($node, $imgNode);
			}
		}
		
		// Export the document HTML
		$html = $doc->saveHTML();
		
		// Set response body
		$response->setBody($html);
	}
	
	/**
	 * This method is called after installing the plugin from the back-end
	 * 
	 * @return void
	 */
	public function install()
	{
		// Clear the caching script
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->script()->cleanCaching();
	}
	
	/**
	 * This method is called after uninstalling the plugin from the back-end
	 * 
	 * @return void
	 */
	public function uninstall()
	{
		// Clear the caching script
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->script()->cleanCaching();
	}
}
