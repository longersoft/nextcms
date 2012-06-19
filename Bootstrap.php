<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	bootstrap
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Main entry point of the app
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Registers autoload
	 * 
	 * @return void
	 */
	protected function _initAutoload()
	{
		// Autoload any class inside the modules directory
		require_once APP_ROOT_DIR . DS . 'modules/core/base/Autoloader.php';
		require_once APP_ROOT_DIR . DS . 'modules/core/base/File.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$modules    = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
		foreach ($modules as $module) {
			new Core_Base_Autoloader(array(
				'basePath'  => APP_ROOT_DIR . DS . 'modules' . DS . $module,
				'namespace' => ucfirst($module) . '_',
			));
		}
		
		require_once 'HTMLPurifier/HTMLPurifier/Bootstrap.php';
		HTMLPurifier_Bootstrap::registerAutoload();

		return $autoloader;
	}
	
	/**
	 * Checks if the app is installed or not. Redirect to the install page if not
	 * 
	 * @return void
	 */
	protected function _initInstallChecker()
	{
		$config = Core_Services_Config::getAppConfigs();
		if (!isset($config['install']) || !isset($config['install']['date'])) {
			header('Location: install.php');
			exit();
		}
	}
	
	/**
	 * Loads routes
	 * 
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
		// Load routes of all modules
		Core_Services_Route::loadRoutes();
		
		$front = $this->getResource('FrontController');
		// Remove default routes
		$front->getRouter()->removeDefaultRoutes();
	}
	
	/**
	 * Initializes the session handler
	 * 
	 * @return void
	 */
	protected function _initSession()
	{
		// Registry session handler
		Core_Services_Db::connect('slave');
		
		$handler = Core_Services_Config::get('core', 'session_handler', 'db');
		switch ($handler) {
			case 'db':
				Zend_Session::setSaveHandler(Core_Services_Session_DatabaseHandler::getInstance());
				break;
			case 'memcache':
				Zend_Session::setSaveHandler(Core_Services_Session_MemcacheHandler::getInstance());
				break;
			case 'file':
			default:
				break;
		}
		
		Zend_Session::setOptions(array(
			'cookie_lifetime' => Core_Services_Config::get('core', 'session_cookie_lifetime', 3600),
			'cookie_domain'   => Core_Services_Config::get('core', 'session_cookie_domain', ''),
		));
		
		// Keep the session if we see the PHPSESSID param passed from request.
		// The most popular case is Flash uploaders have to pass the session Id
		// as a separate param since they can not store the session.
		if (isset($_GET['PHPSESSID'])) {
			session_id($_GET['PHPSESSID']);
		} else if (isset($_POST['PHPSESSID'])) {
			session_id($_POST['PHPSESSID']);
		}
	}
	
	/**
	 * Initializes view
	 * 
	 * @return void
	 */
	protected function _initView()
	{
		$view = new Core_Base_View();
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$viewRenderer->setView($view);
		
		Zend_Controller_Action_HelperBroker::addPath(APP_ROOT_DIR . DS . 'modules/core/controllers/actions/helpers',
													 'Core_Controllers_Actions_Helpers');
		return $view;
	}
	
	/**
	 * Initializes hooks
	 * 
	 * @return void
	 */
	protected function _initHooks()
	{
		// Sanitize input data
		Core_Base_Hook_Registry::getInstance()->register('Core_SanitizeInput', array(HTMLPurifier::getInstance(), 'purify'));
	}
	
	/**
	 * Registers plugins
	 * 
	 * @return void
	 */
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		
		// Register plugins.
		// ZF LESSON: The alternative way is that put plugin to /configs/application.{APP_ENV}.php:
		// 		$config['resources']['frontController']['plugins']['pluginName'] = "Plugin_Class";
		$front->registerPlugin(new Core_Controllers_Plugins_Init())
			  ->registerPlugin(new Core_Controllers_Plugins_Template())
			  ->registerPlugin(new Core_Controllers_Plugins_L10NRoute())
			  ->registerPlugin(new Core_Controllers_Plugins_PageMapper())
			  ->registerPlugin(new Core_Controllers_Plugins_Auth())
			  ->registerPlugin(new Core_Controllers_Plugins_UrlTracker())
			  ->registerPlugin(new Core_Controllers_Plugins_Permalink())
			  ->registerPlugin(new Core_Controllers_Plugins_AccessLogger())
			  ->registerPlugin(new Core_Controllers_Plugins_ActiveModule())
			  ->registerPlugin(new Core_Controllers_Plugins_HookLoader())
			  // Error handler
			  ->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
				  'module'	   => 'core',
				  'controller' => 'notification',
				  'action'	   => 'log',
			  )));
		
		// Register installed plugins from the DB
		Core_Services_Db::connect('slave');
		$plugins = Core_Services_Plugin::getInstalledPlugins(true);
		foreach ($plugins as $plugin) {
			$pluginClass = ucfirst(strtolower($plugin->module)) . '_Plugins_' . ucfirst(strtolower($plugin->name)) . '_Plugin';
			if (class_exists($pluginClass)) {
				$pluginInstance = new $pluginClass();
				if ($pluginInstance instanceof Core_Base_Controllers_Plugin) {
					$front->registerPlugin($pluginInstance);
				}
			}
		}
	}
}
