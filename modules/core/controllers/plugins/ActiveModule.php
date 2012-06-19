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
 * @version		2012-04-24
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Support module bootstrapping. Also, the module bootstrapper is called
 * if and only if the a module route is requested.
 * Based on module bootstrapping article of Chris Woodford
 *  
 * @see http://offshootinc.com/blog/2011/02/11/modul-bootstrapping-in-zend-framework/
 */
class Core_Controllers_Plugins_ActiveModule extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::routeShutdown()
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		// Get the default bootstrap
		$frontController = Zend_Controller_Front::getInstance();
		$bootstrap		 = $frontController->getParam('bootstrap');
//		$moduleList		 = $bootstrap->getResource('modules');
		$moduleList		 = $bootstrap->getResource('Core_Application_Resource_Modules');
		
		// Get the current module
		$currentModule	 = $request->getModuleName();
		
		// Get the bootstrap object for current module
		if ($currentModule == null || !isset($moduleList[$currentModule])) {
			return;
		}
		
		$activeBootstrap = $moduleList[$currentModule];
		if ($activeBootstrap instanceof Core_Base_Application_Module_Bootstrap) {
			$className = ucfirst($currentModule) . '_Bootstrap_Initializer';
			if (class_exists($className)) {
				$intializer = new $className($activeBootstrap);
				$intializer->initialize();
			}
		}
	}
}
