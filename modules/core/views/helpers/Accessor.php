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

class Core_View_Helper_Accessor extends Zend_View_Helper_Abstract 
{
	/**
	 * List of routes
	 * 
	 * @var array
	 */
	private $_routes;
	
	/**
	 * @return Core_View_Helper_Accessor
	 */
	public function accessor()
	{
		return $this;
	}
	
	/**
	 * Checks if user can access page defined by given route
	 * 
	 * @param string $routeName Name of route
	 * @return bool
	 */
	public function route($routeName)
	{
		// Cache list of routes
		if ($this->_routes == null) {
			$router		   = Zend_Controller_Front::getInstance()->getRouter();
			$this->_routes = $router->getRoutes();
		}
		if (!isset($this->_routes[$routeName])) {
			return false;
		}
		// Get the route
		$routes   = $this->_routes[$routeName];
		
		// Get the array of action, controller and module associated with route
		$defaults = $routes->getDefaults();

		return Core_Services_Rule::isAllowed($defaults['action'], $defaults['controller'], $defaults['module']);
	}
	
	/**
	 * Checks if user can access action which has the same module, controller with current page
	 * 
	 * @param string $action Name of action
	 * @return bool
	 */
	public function action($action)
	{
		return Core_Services_Rule::isAllowed($action);
	}
}
