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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Override the URL view helper
 */
class Core_View_Helper_Url extends Zend_View_Helper_Url
{
	/**
	 * @see Zend_View_Helper_Url::url()
	 */
	public function url(array $urlOptions = array(), $name = null)
	{
		$url	= parent::url($urlOptions, $name);
		$router = Zend_Controller_Front::getInstance()->getRouter();
		
		if ($router instanceof Zend_Controller_Router_Rewrite) {
			Core_Services_Db::connect('slave');
			
			$route	  = $router->getRoute($name);
			$defaults = $route->getDefaults();
			
			// Add token to URL if the CSRF protection is enable
			if (isset($defaults['csrf']) && $defaults['csrf']['enabled'] && 'get' == $defaults['csrf']['retrive']) {
				$csrfHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Csrf');
				return $url . '?' . $csrfHelper->getTokenName() . '=' . $csrfHelper->getToken();
			}
			
			// Append the language to the beginning of URI
			// if it is localizable route
			if (isset($defaults['localization']) && $defaults['localization']['enabled']
				&& 'true' == Core_Services_Config::get('core', 'localization_enabled', 'false')) 
			{
				$lang	   = isset($urlOptions['language'])
								? $urlOptions['language']
								: Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
				$baseUrl   = $this->view->baseUrl();
				$serverUrl = $this->view->serverUrl();
				$path	   = substr($serverUrl . $url, strlen($baseUrl));		
				//$url  	   = rtrim($baseUrl, '/') . '/' . $lang . '/' . ltrim($path, '/');
				$newUrl	   = rtrim(substr($baseUrl, strlen($serverUrl)), '/') . '/' . $lang . '/' . ltrim($path, '/');
				return $newUrl;
			}
		}
		
		return $url;
	}
}
