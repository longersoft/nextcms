<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	install
 * @since		1.0
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Install Wizard
 */
class Installer extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Registers autoload
	 * 
	 * @return void
	 */
	protected function _initAutoload()
	{
		// Autoloads any class inside the modules directory
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

		return $autoloader;
	}
	
	/**
	 * Inits view
	 * 
	 * @return void
	 */
	protected function _initView()
	{
		// Gets view instance
		$view = new Core_Base_View();
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$viewRenderer->setView($view);
		
		$view->addHelperPath(APP_ROOT_DIR . DS . 'modules/core/views/helpers', 'Core_View_Helper');

		// Sets the language that will be used in the Install Wizard.
		// I have to set it here because the Translator view helper try to determine the language by getting
		// from the database which cannot access (or has not been defined) at that time.
		$config	  = Core_Services_Config::getAppConfigs();
		$language = ($config && isset($config['install']['language']))
					? $config['install']['language']
					: 'en_US';
		$view->translator()->setLanguage($language);

		// Shows the message and exit if the app is already installed
		if (isset($config['install']) && isset($config['install']['date'])) {
			die($view->translator()
					 ->setLanguageDir('/modules/core/languages')
					 ->_('install._share.alreadyInstalled'));

			// Or redirects to the index page if it was installed:
			//		Core_Services_Db::connect('slave');
			//		$url = Core_Services_Config::get('core', 'url_base');
			//		header('Location: ' . $url);
			//		exit();
		}

		// Builds the root URL
		$request  = new Zend_Controller_Request_Http();
		$siteUrl  = $request->getScheme() . '://' . $request->getHttpHost();
		$basePath = $request->getBasePath();
		$siteUrl  = ($basePath == '') ? $siteUrl : $siteUrl . '/' . ltrim($basePath, '/');

		$view->assign(array(
			'APP_ROOT_URL'	   => $siteUrl,
			'APP_STATIC_URL'   => $siteUrl,
			'APP_LANGUAGE'	   => $language,
			'APP_LANGUAGE_DIR' => ($config && isset($config['install']['language_direction'])) ? $config['install']['language_direction'] : 'ltr',
		));
		
		// Sets layout
		Zend_Layout::startMvc(array(
			'layoutPath' => APP_ROOT_DIR . DS . 'templates' . DS . 'admin' . DS . 'layouts',
		));
		Zend_Layout::getMvcInstance()->setLayout('install');
	}

	/**
	 * Loads routes
	 * 
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
		$front  = $this->getResource('FrontController');
		$router = $front->getRouter();

		// I do NOT use default routes
		$router->removeDefaultRoutes();

		// Adds installation routes manually
		$router->addRoute('core_install_index',
					new Zend_Controller_Router_Route('/', array(
						'module' 	 => 'core',
						'controller' => 'Install',
						'action' 	 => 'index',
					))
				)
				->addRoute('core_install_check',
					new Zend_Controller_Router_Route('/check/', array(
						'module' 	 => 'core', 
						'controller' => 'Install', 
						'action' 	 => 'check',
					))
				)
				->addRoute('core_install_config',
					new Zend_Controller_Router_Route('/config/', array(
						'module' 	 => 'core', 
						'controller' => 'Install', 
						'action' 	 => 'config',
					))
				)
				->addRoute('core_install_complete',
					new Zend_Controller_Router_Route('/complete/', array(
						'module' 	 => 'core', 
						'controller' => 'Install', 
						'action' 	 => 'complete',
					))
				)
				->addRoute('core_install_testdb',
					new Zend_Controller_Router_Route('/testdb/', array(
						'module' 	 => 'core', 
						'controller' => 'Install',
						'action' 	 => 'testdb',
					))
				)
				->addRoute('core_password_generate',
					new Zend_Controller_Router_Route('/core/password/generate/', array(
						'module' 	 => 'core', 
						'controller' => 'password',
						'action' 	 => 'generate',
					))
				);
	}

	/**
	 * Inits plugins
	 * 
	 * @return void
	 */
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		$front->registerPlugin(new Core_Base_Controllers_Plugins_MagicQuote());
	}
}
