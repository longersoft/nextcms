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
 * @version		2012-05-04
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Controllers_Plugins_Init extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		Core_Services_Db::connect('slave');
		
		$config = Core_Services_Config::getAppConfigs();
		$view   = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		//$view->doctype('XHTML1_STRICT');
		$view->addHelperPath(APP_ROOT_DIR . DS . 'modules/core/views/helpers', 'Core_View_Helper');
		
		// Set base URL
		//$view->getHelper('BaseUrl')->setBaseUrl(Core_Services_Config::get('core', 'url_base'));
		
		// Append meta tags
		$view->headTitle()->set(Core_Services_Config::get('core', 'site_name', ''));
		$view->headTitle()->setSeparator(' - ');
		$view->headMeta()->appendName('description', Core_Services_Config::get('core', 'meta_description', ''));
		$view->headMeta()->setName('keywords', Core_Services_Config::get('core', 'meta_keyword', ''));
		
		// Set timezone
		@date_default_timezone_set(Core_Services_Config::get('core', 'datetime_timezone', @date_default_timezone_get()));
		
		// Set charset
		$charset = Core_Services_Config::get('core', 'charset', 'utf-8');
		$this->getResponse()->setHeader('Content-Type', 'text/html; charset=' . $charset);
		
		// Set theme for site. User can change skin at real time. 
		// Check whether user set skin cookie or not
		$skin	  = (isset($_COOKIE['APP_SKIN'])) ? $_COOKIE['APP_SKIN'] : Core_Services_Config::get('core', 'skin', 'default'); 
		$template = Core_Services_Template::getCurrentTemplate();
		
		$view->assign(array(
			'APP_SKIN'		   => $skin,
			'APP_TEMPLATE'	   => $template,
			'APP_URL'		   => $view->serverUrl() . Core_Services_Config::get('core', 'url_base'),
			'APP_ROOT_URL'	   => $view->serverUrl() . $view->baseUrl(),
			'APP_STATIC_URL'   => Core_Services_Config::get('core', 'url_static'),
			'APP_CHARSET'	   => $charset,
			
			// Support RTL language
			'APP_LANGUAGE_DIR' => Core_Services_Config::get('core', 'language_direction', 'ltr'),
			'APP_LANGUAGE'	   => Core_Services_Config::get('core', 'language_code', 'en_US'),
		));
		
		// Set layout
		Zend_Layout::startMvc(array(
			'layoutPath' => APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'layouts',
		));
		Zend_Layout::getMvcInstance()->setLayout('default');
		
		// Cache language data if user use caching system
		$cache = Core_Services_Cache::getInstance();
		if ($cache) {
			Zend_Translate::setCache($cache);
		}
	}
}
