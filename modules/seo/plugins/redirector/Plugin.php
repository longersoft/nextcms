<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		seo
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Seo_Plugins_Redirector_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		if ($request->isXmlHttpRequest()) {
			return;
		}
		$currentUri = $request->getRequestUri();
		if (null == $currentUri || '' == $currentUri) {
			return;
		}
		
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		if ($options == null || !isset($options['urls'])) {
			return;
		}
		
		$view	    = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$currentUri = rtrim($view->serverUrl(), '/') . '/' . ltrim($currentUri, '/');
		$currentUri = self::_normalizeUri($currentUri);
		$urls		= $options['urls'];
		
		if (isset($urls[$currentUri])) {
			$destinationUrl = $urls[$currentUri];
			
			// Redirect to the destination page
			$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
			$helper->gotoUrl($destinationUrl);
			exit();
		}
	}
	
	/**
	 * Normalizes an URL
	 * 
	 * @param string $url The URL
	 * @return string
	 */
	private static function _normalizeUri($url)
	{
		$view	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$rootUrl = $view->APP_URL;
		
		if (strlen($url) >= strlen($rootUrl) && substr($url, 0, strlen($rootUrl)) == $rootUrl) {
			$url = substr($url, strlen($rootUrl));
		}
		
		$url = ltrim($url, '/');
		return '/' . rtrim($url, '/');
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		$urls	 = ($options == null || !isset($options['urls'])) ? array() : $options['urls'];
		$this->view->assign('urls', $urls);
	}
	
	/**
	 * Saves the urls
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request			= $this->_extension->getRequest();
		$sourceUrls			= $request->getParam('source_urls');
		$destinationUrlUrls	= $request->getParam('destination_urls');
		
		$result	= false;
		if ($sourceUrls && count($sourceUrls) > 0) {
			$urls = array();
			for ($i = 0; $i < count($sourceUrls); $i++) {
				$source		 = $sourceUrls[$i];
				$destination = $destinationUrlUrls[$i];
				
				if ($source != '' && $destination != '') {
					$source		   = self::_normalizeUri($source);
					$destination   = self::_normalizeUri($destination);
					
					if ($source != $destination) {
						$urls[$source] = $destination;
					}
				}
			}
			if (count($urls) > 0) {
				$result = Core_Services_Plugin::setOptionsForInstance($this, array('urls' => $urls));				
			}
		}
		return $result ? 'true' : 'false';
	}
}
