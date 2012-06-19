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
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Install controller
 */
class Core_InstallController extends Zend_Controller_Action
{
	/**
	 * Required extensions
	 * 
	 * @var array
	 */
	private static $_REQUIRED_EXTENSIONS = array(
		'gd',
		'json',
		'mbstring',
		'mcrypt',		// To encrypt and decrypt URL
		'simplexml', 
		'xml',
		'xmlreader',
	);

	/**
	 * The folders/files need to have a write permission
	 * 
	 * @var array
	 */
	private static $_REQUIRED_WRITABLE_FILES = array(
		'configs', 			// Contains config file
		'templates',		// Contains the template file which might be changed
		'temp',				// Contains the temp files (caching files)
		'upload',			// For uploading data
		'robots.txt',
	);
	
	/**
	 * Support multiple databases
	 * 
	 * @var array
	 */
	private static $_SUPPORTED_DATABASES = array(
		// MySQL with Native driver
		'mysql'	=> array(
			'name' 		 => 'MySQL',
			'extensions' => array('mysql'),
			'port'		 => 3306,
		), 
		// MySQL with Pdo driver
		'pdo_mysql' => array(
			'name' 		 => 'MySQL (Pdo)',
			'extensions' => array('mysql', 'pdo', 'pdo_mysql'),
			'port'		 => 3306,
		),
	);	
	
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		// Disable layout
		$this->_helper->getHelper('layout')->disableLayout();
	}
	
	/**
	 * Index action.
	 * This action just shows layout only
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$this->_helper->getHelper('layout')->enableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
	}
	
	/**
	 * Checks requirements
	 * 
	 * @return void
	 */
	public function checkAction()
	{
		$pass 		= true;
		$extensions = array();
		$files 		= array();
		
		foreach (self::$_REQUIRED_EXTENSIONS as $ext) {
			$extensions[$ext] = extension_loaded($ext);
			$pass = $pass && $extensions[$ext];
		}
		
		foreach (self::$_REQUIRED_WRITABLE_FILES as $file) {
			$files[$file] = is_writeable(APP_ROOT_DIR . DS . $file);
			$pass = $pass && $files[$file];
		}
		
		$this->view->assign(array(
			'extensions' => $extensions,
			'files'		 => $files,
			'pass'		 => $pass,
		));
	}
	
	/**
	 * Completes installation
	 * 
	 * @return void
	 */
	public function completeAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		switch ($format) {
			case 'json':
				$prefix = $request->getParam('prefix');
				
				// Set the prefix of admin URLs
				Core_Services_Config::set('core', 'admin_prefix', $prefix);
				
				// Add admin user
				$result = Core_Services_Installer::addRootUser($request->getParam('password'), $request->getParam('email'));
				
				// Update the config file to indicate that the app is installed
				if ($result) {
					$config = Core_Services_Config::getAppConfigs();
					$config['install']['date']    = date('Y-m-d H:i:s');
					$config['install']['version'] = Core_Services_Version::getVersion();
					Core_Services_Config::writeAppConfigs($config);
				}
				
				// Defines the admin URL
				$baseUrl = $this->view->baseUrl();
				if ('' != $baseUrl) {
					$baseUrl = rtrim($baseUrl, '/');
					$baseUrl = ltrim($baseUrl, '/');
				}
				$url  = ('' == $baseUrl) ? $this->view->APP_ROOT_URL : $this->view->APP_ROOT_URL . '/index.php';
				$url .= '/' . $prefix;
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'url'	 => $url,
				));
				break;
			default:
				// Generate random password
				$this->view->assign('password', Core_Services_User::generatePassword());
				break;
		}
	}	
	
	/**
	 * Configs the app
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		switch ($format) {
			case 'json':
				// Get the submit data via Ajax
				$dbType = $request->getParam('type');
				
				$config = Core_Services_Config::getAppConfigs();
				
				if (get_magic_quotes_gpc()) {
					$config['resources']['frontController']['plugins']['magicQuote'] = 'Core_Base_Controllers_Plugins_MagicQuote';	
				} else {
					unset($config['resources']['frontController']['plugins']['magicQuote']);
				}
				
				// Set base URL
				$siteUrl  = $request->getScheme() . '://' . $request->getHttpHost();
				$basePath = $request->getBasePath();
				if ($basePath != '') {
					$basePath = ltrim($basePath, '/');
					$basePath = rtrim($basePath, '/');
				}
				$url 	   = ($basePath == '') ? $siteUrl : $siteUrl . '/' . $basePath . '/index.php';
				$staticUrl = ($basePath == '') ? $siteUrl : $siteUrl . '/' . $basePath;
				$baseUrl   = ($basePath == '') ? '' : '/' . $basePath . '/index.php';
				
				$config['resources']['frontController']['baseUrl'] = $baseUrl;
				
				// Set DB settings
				unset($config['db']);
				$config['db']['adapter'] = $dbType;
				$config['db']['prefix']  = $request->getPost('prefix', '');
				$config['db']['slave']['server1']['host'] 	  = $config['db']['master']['server2']['host'] 	   = $request->getParam('host');
				$config['db']['slave']['server1']['port'] 	  = $config['db']['master']['server2']['port']     = $request->getParam('port');
				$config['db']['slave']['server1']['host']     = $config['db']['master']['server2']['host']     = $request->getParam('server');
				$config['db']['slave']['server1']['dbname']   = $config['db']['master']['server2']['dbname']   = $request->getParam('dbName');
				$config['db']['slave']['server1']['username'] = $config['db']['master']['server2']['username'] = $request->getParam('username');
				$config['db']['slave']['server1']['password'] = $config['db']['master']['server2']['password'] = $request->getParam('password'); 
				
				// Write configurations to file
				Core_Services_Config::writeAppConfigs($config);
				
				// Now the database settings have just changed. I have to unset all objects registered by 
				// Zend_Registry to ensure that I will get fresh instance of some objects 
				// before doing other install tasks
				Zend_Registry::_unsetInstance();

				Core_Services_Db::connect('master');
				
				// Install the Core module first
				Core_Services_Module::install('core');
				
				// Load the module routes, so I can get the route URL in the installing callbacks
				Core_Services_Route::loadRoutes();
				$request->setBaseUrl($baseUrl);
				
				// Install
				Core_Services_Installer::installApp();
				
				// Set the most important settings, such as base URL
				$defaultSettings = array(
					array('core', 'url_base', 					   $baseUrl),
					array('core', 'url_static',					   $staticUrl),
					array('core', 'datetime_timezone',			   @date_default_timezone_get()),
					array('core', 'charset',					   'utf-8'),
					// FIX ME: Get the Install Wizard language
					array('core', 'language_code',				   'en_US'),
					array('core', 'localization_default_language', 'en_US'),
					array('core', 'session_cookie_lifetime', 	   3600),
					array('core', 'session_handler', 			   'db'),
					array('core', 'template', 					   'default'),
					array('core', 'skin', 						   'default'),
				);
				foreach ($defaultSettings as $item) {
					list($module, $key, $value) = $item;
					Core_Services_Config::set($module, $key, $value);
				}
				
				// Install the default template
				Core_Services_Template::install('default');
				
				// Response
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				// List of supported Dbs
				$databases = self::$_SUPPORTED_DATABASES;
				foreach (self::$_SUPPORTED_DATABASES as $db => $info) {
					if (is_array($info['extensions'])) {
						foreach ($info['extensions'] as $ext) {
							if (!extension_loaded($ext)) {
								$databases[$db]['disabled'] = true;
								//unset($databases[$db]);
								break;
							}
						}
					}
				}
		
				$this->view->assign(array(
					'databases'		=> $databases,
					'jsonDatabases' => Zend_Json::encode($databases),
				));
				break;
		}
	}

	/**
	 * Checks DB connection
	 * 
	 * @return void
	 */
	public function testdbAction()
	{
		$request = $this->getRequest();
		$type 	 = $request->getPost('type', 'mysql');
		$db 	 = Core_Services_Db::factory($type);

		$options = array(
			'host'     => $request->getPost('host', 'localhost'),
			'port' 	   => $request->getPost('port', self::$_SUPPORTED_DATABASES[$type]['port']),
			'username' => $request->getPost('username'),
			'password' => $request->getPost('password'),
			'dbname'   => $request->getPost('dbName', ''),
		);

		$success = $db->testConnection($options);
		if ($success === false) {
			$return    = array(
				'result'	=> 'APP_RESULT_ERROR',
				'databases' => null,
			);
		} else {
			$databases = $db->getDatabases($options);
			$return    = array(
				'result'	=> 'APP_RESULT_OK',
				'databases' => $databases,
			);
		}

		$this->_helper->json($return);
	}
}
