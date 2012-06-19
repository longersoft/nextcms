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
 * @subpackage	base
 * @since		1.0
 * @version		2012-05-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Extension
{
	/**
	 * Name of module
	 * 
	 * @var string
	 */
	protected $_module;
	
	/**
	 * Name of extension
	 * 
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Original paths of view helpers
	 * 
	 * @var array
	 */
	protected $_originalHelperPaths;
	
	/**
	 * @var array
	 */
	protected $_helperPaths = array();
	
	/**
	 * @var array
	 */
	protected $_scriptPaths = array();
	
	/**
	 * @var string
	 */
	protected $_languagePath;
	
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;
	
	/**
	 * @var Zend_Controller_Response_Abstract
	 */
	protected $_response;
	
	/**
	 * The parameter indicates no theming mode
	 * @var string
	 */
	const PARAM_NO_THEMING = 'noTheming';
	
	/**
	 * @var Zend_View_Abstract
	 */
	public $view;
	
	public function __construct($name, $module)
	{
		$this->_name   = strtolower($name);
		$this->_module = strtolower($module);
		
		$this->_init();
	}
	
	/**
	 * Gets the extension's module
	 * 
	 * @return string
	 */
	public function getModule()
	{
		return $this->_module;
	}
	
	/**
	 * Gets the extension's name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Disables the extension
	 * 
	 * @return void
	 */
	public function disable()
	{
	}

	/**
	 * Enables the extension
	 * 
	 * @return void
	 */
	public function enable()
	{
	}
	
	/**
	 * Installs the extension
	 * 
	 * @return void
	 */
	public function install()
	{
	}

	/**
	 * Uninstalls the extension
	 * 
	 * @return void
	 */
	public function uninstall()
	{
	}
	
	/**
	 * Renders an action of the extension
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return string
	 */
	public function __call($name, $arguments)
	{
		$this->_reset();
		
		if ($arguments != null && is_array($arguments) && count($arguments) > 0) {
			if ($arguments[0] != null && is_array($arguments[0])) {
				$this->_request->setParams($arguments[0]);
			}
		}
		
		// Return if a xxxAction() method is called directly
		if (strlen($name) > 6 && substr($name, -6) == 'Action') {
			return '';
		}
		
		foreach ($this->_helperPaths as $path) {
			$this->view->addHelperPath($path['path'], $path['prefix']);
		}
		foreach ($this->_scriptPaths as $path) {
			$this->view->addScriptPath($path);
		}
		if ($this->_languagePath) {
			$this->view->translator()->setLanguageDir($this->_languagePath);
		}

		$method = $name . 'Action';
		if (method_exists($this, $method)) {
			// FIXME: Create a constant for the special variable named "noRenderScript"
			if ($this->_request->getParam('noRenderScript') == true) {
				return $this->$method();
			} else {
				$this->$method();
			}
		}
		
		// Render the script
		$theme   = $this->_request->getParam('theme', '');
		if ($this->_request->getParam(self::PARAM_NO_THEMING, 'false') == 'true') {
			$theme = '';
		}
		
		$format  = $this->_request->getParam('format', '');
		$script  = $theme ? ($name . '.' . $theme) : $name;
		$script  = $format ? ($script . '.' . $format . '.phtml') : ($script . '.phtml');
		
		$content = $this->view->render($script);
		$this->_response->appendBody($content);
		
		$body = $this->_response->getBody();
		$this->_reset();
		
		// Helpers for extension have the same name ("helper"), hence in order to
		// the next widget call exactly its helper, we have to reset the helper paths
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view; 
		$view->setHelperPath(null);
		if ($this->_originalHelperPaths) {
			foreach ($this->_originalHelperPaths as $prefix => $paths) {
				foreach ($paths as $path) {
					$view->addHelperPath($path, $prefix);
				}
			}
		}
		
		return $body;
	}
	
	/**
	 * Clones the request, response and view instance
	 * 
	 * @return void
	 */
	private function _init()
	{
		// Clone the request, response and view instance. It also supports
		// cloning in non-MVC environment to ensure that we can creat
		// widget controller instance, for example, when running the cron jobs.
		$front	  = Zend_Controller_Front::getInstance();
		$request  = $front->getRequest();
		$response = $front->getResponse();
		
		$this->_request	 = ($request == null) ? new Zend_Controller_Request_Http() : clone $request;
		$this->_response = ($response == null) ? new Zend_Controller_Response_Http() : clone $response;
		$viewRenderer	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$this->view		 = ($viewRenderer->view == null) ? new Core_Base_View() : (clone $viewRenderer->view);
		
		$this->_originalHelperPaths = $this->view->getHelperPaths();
	}
	
	/**
	 * Resets the request, response and view instance
	 * 
	 * @return void
	 */
	private function _reset()
	{
		$params = $this->_request->getUserParams(); 
		foreach (array_keys($params) as $key) {
			$this->_request->setParam($key, null);
		}

		$this->_response->clearBody();
		$this->_response->clearHeaders()->clearRawHeaders();
	}
	
	/**
	 * @return Zend_Controller_Request_Abstract
	 */
	public function getRequest()
	{
		return $this->_request;
	}
	
	/**
	 * Sets the path that the view can find the helpers
	 * 
	 * @param string $path
	 * @param string $prefix
	 * @return Core_Base_Extension
	 */
	public function addHelperPath($path, $prefix)
	{
		$this->_helperPaths[] = array(
			'path'	 => $path,
			'prefix' => $prefix,
		);
		return $this;
	}
	
	/**
	 * Sets the path that the view can find the view scripts to render
	 * 
	 * @param string $path
	 * @return Core_Base_Extension
	 */
	public function addScriptPath($path)
	{
		$this->_scriptPaths[] = $path;
		return $this;
	}
	
	/**
	 * Sets the path of the languages directory
	 * 
	 * @param string $path
	 * @return Core_Base_Extension
	 */
	public function setLanguagePath($path)
	{
		$this->_languagePath = $path;
		$this->view->translator()->setLanguageDir($path);
		return $this;
	}
}
