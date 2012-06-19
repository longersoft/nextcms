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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * This plugin stores the current URL in session.
 * If you don't want to track the URL, add the following line to the
 * associated route configuration:
 * 
 * 		$routes[routeName]['defaults']['track']['enabled'] = "false";
 * 
 * where routeName is the route name (in format of module_controller_action).
 */
class Core_Controllers_Plugins_UrlTracker extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * Session key which is used to save the URL
	 * 
	 * @var const
	 */
	const SESSION_NS = 'Core_Controllers_Plugins_UrlTracker';
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Combine the current module, controller and action
		$mca    = implode('_', array(
								$request->getModuleName(),
								$request->getControllerName(),
								$request->getActionName(),
							));
		$mca    = strtolower($mca);
		$router = Zend_Controller_Front::getInstance()->getRouter();
		
		if ($router instanceof Zend_Controller_Router_Rewrite && $router->hasRoute($mca)) {
			// Use it to define the current route
			$route    = $router->getRoute($mca);
			$defaults = $route->getDefaults();
			
			if (!isset($defaults['track']) || $defaults['track']['enabled']) {
				$session = new Zend_Session_Namespace(self::SESSION_NS);
				// ZF LESSON: If I access the following URL: 
				// http://localhost/cms/index.php/admin/dashboard#u=/cms/index.php/admin/xxx
				// then here are the URLs provied by $request:
				//		$request->getBasePath()   => /cms
				//		$request->getBaseUrl()	  => /cms/index.php (The same as $request->getServer('SCRIPT_NAME))
				//		$request->getPathInfo()   => /admin/dashboard (same as $request->getServer('PATH_INFO'))
				// 		$request->getRequestUri() => /cms/index.php/admin/dashboard (same as $request->getServer('PHP_SELF'))
				$session->url		   = $request->getRequestUri();
				$session->isXhrRequest = $request->isXmlHttpRequest();
				
				// The fragement of the URL (u=/cms/index.php/admin/xxx, in the above example) 
				// is not sent to the server, so I cannot get it.
			}
		}
	}
}
