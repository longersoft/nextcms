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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Parses the URL and detects the current used language if localization is enable
 */
class Core_Controllers_Plugins_L10NRoute extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		Core_Services_Db::connect('slave');
		
		$currUri	 = rtrim($request->getRequestUri(), '/');
		$defaultLang = Core_Services_Config::get('core', 'language_code', 'en_US');
		$currLang	 = $defaultLang;
		
		// I am in the front-end section
		$adminPrefix = Core_Services_Config::get('core', 'admin_prefix', 'admin');
		if (strpos(strtolower($currUri) . '/', '/' . $adminPrefix . '/') === false) {
			$paths	  = explode('/', ltrim($request->getPathInfo(), '/'));
			$currLang = array_shift($paths);
		} 
		// in the back-end section
		else {
			$paths	  = explode('/', rtrim($request->getPathInfo(), '/'));
			$currLang = array_pop($paths);
		}
		
		// Add language parameter. Set the request URI if there is language in URI
		$localizationLanguages = Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}');
		$localizationLanguages = Zend_Json::decode($localizationLanguages);
		if (isset($localizationLanguages[$currLang])) {
			$request->setParam('lang', $currLang);
			$path = implode('/', $paths);
			if ('' == $path) {
				$path = '/';
			}
			$request->setPathInfo($path);
		} else {
			$request->setParam('lang', $defaultLang);
		}
	}
}
