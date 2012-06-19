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
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Base on the request URL and role/permisson of current user, forward the user
 * to the login page if the user have not logged in 
 */
class Core_Controllers_Plugins_Auth extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		Core_Services_Db::connect('master');
		
		$uri		 = $request->getRequestUri();
		$uri		 = strtolower($uri);
		$uri		 = rtrim($uri, '/') . '/';
		$adminPrefix = Core_Services_Config::get('core', 'admin_prefix', 'admin');
		if (strpos($uri, '/' . $adminPrefix . '/') === false) {
			return;
		}
		
		// Switch to admin template
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		Zend_Layout::startMvc(array('layoutPath' => APP_ROOT_DIR . DS . 'templates' . DS . 'admin' . DS . 'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('admin');
		
		$isAllowed = false;
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$user		= Zend_Auth::getInstance()->getIdentity();
			$module		= $request->getModuleName();
			$controller = $request->getControllerName();
			$action		= $request->getActionName();
			
			// Add 'core:notification' resource that allows show the friendly error message
			$acl = Core_Services_Acl::getInstance();
			if (!$acl->has('module:core:notification')) {
				$acl->addResource('module:core:notification');
			}
			
			$routes	   = Zend_Controller_Front::getInstance()->getRouter()->getRoutes();
			$routeName = strtolower($module . '_' . $controller . '_' . $action);
			if (!isset($routes[$routeName])) {
				throw new Core_Base_Exception_NotFound('Cannot find a route named ' . $routeName);
			} else {
				$defaults = $routes[$routeName]->getDefaults();
				if (isset($defaults['allowed'])) {
					// In the back-end section, there are some pages that any logged-in user have permission to access, such as
					// the dashboard, the pages for updating account information, changing the password, etc.
					// In this case, the system will check for "allowed" property defined in the route configuration.
					// Below is an example of configuration:
					//		'module_controller_action' => array(
					//			'defaults' => array(
					//				'allowed' => true,
					//			),
					//		),
					// By default, the "allowed" property is not defined or gets the FALSE value.
					if (is_bool($defaults['allowed'])) {
						$isAllowed = $defaults['allowed'];
					} else {
						// The action permission is depended on other one:
						//		'module_controller_action' => array(
						//			'defaults' => array(
						//				'allowed' => 'otherModule_otherController_otherAction',
						//			),
						//		),
						list($module, $controller, $action) = explode('_', $defaults['allowed']);
						$isAllowed = $acl->isUserOrRoleAllowed($module, $controller, $action);
					}
				} else {
					$isAllowed = $acl->isUserOrRoleAllowed($module, $controller, $action);
				}
			}
			
			if ($request->isXmlHttpRequest()) {
				// The back-end actions will be loaded using Ajax request, therefore 
				// the layout will be disabled
				Zend_Layout::getMvcInstance()->disableLayout();
			}
		}
		if (!$isAllowed) {
			$forwardAction = Zend_Auth::getInstance()->hasIdentity() ? 'deny' : 'login';
			
			if ($request->isXmlHttpRequest()) {
				echo $view->translator()->setLanguageDir('/modules/core/languages')->_('auth.deny.notAllowed');
				// Reset the language dir
				$view->translator()->setLanguageDir(null);
				exit();
			} else {
				// DON'T use redirect as folow: 
				//		$this->getResponse()->setRedirect('/login/');
				$request->setModuleName('core')
						->setControllerName('Auth')
						->setActionName($forwardAction)
						->setDispatched(true);
			}
		}
	}
}
