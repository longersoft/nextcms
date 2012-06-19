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
 * @version		2012-05-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * This plugin allows to use the customized links
 */
class Core_Controllers_Plugins_Permalink extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		// Don't continue if I am at the page of managing permalink in the back-end
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);
		$uri = rtrim($uri, '/') . '/';
		if (is_int(strpos($uri, '/core/permalink/config'))) {
			return;
		}
		
		$file = APP_ROOT_DIR . DS . 'configs' . DS . APP_HOST_CONFIG . '_permalink.' . strtolower(APP_ENV) . '.php';
		if (!file_exists($file)) {
			return;
		}
		$config = include_once $file;
		if (!is_array($config)) {
			return;
		}
		
		// Remove the route if it is redefined in the file
		$router = Zend_Controller_Front::getInstance()->getRouter();
		foreach ($config as $routeName => $settings) {
			if ($router->hasRoute($routeName)) {
				$router->removeRoute($routeName);
			}
		}
		
		// Add the route with new configurations
		$router->addConfig(new Zend_Config($config));
	}
}
