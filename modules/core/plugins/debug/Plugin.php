<?php
/**
 * NextCMS
 * 
 * Based on the ZFDebug created by Joakim Nygard and Andreas Pankratz.
 * See http://code.google.com/p/zfdebug/ for more information.
 * 
 * @author		Joakim Nygard <http://jokke.dk>
 * @author		Andreas Pankratz <http://www.bangal.de>
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-05-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Debug_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @var bool
	 */
	private $_tracking = false;
	
	/**
	 * Standard plugins
	 * 
	 * @var array
	 */
	private $_standardPlugins = array('database', 'file', 'memory', 'registry', 'system', 'variable');
	
	/**
	 * Array of plugins which are choosen by user
	 * 
	 * @var array
	 */
	private $_plugins = array();
	
	public function __construct()
	{
		Core_Services_Db::connect('master');
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowToolboxPane', array($this, 'debug'), true);
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_core', array($this, 'menu'), true);
		
		$plugins = Core_Services_Plugin::getOptionsByInstance($this, $this->_standardPlugins);
		foreach ($plugins as $plugin) {
			$class = 'Core_Plugins_Debug_Plugins_' . ucfirst($plugin);
			$this->_plugins[$plugin] = new $class();
		}
	}
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch($request)
	{
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$module		= $request->getModuleName();
			$controller = $request->getControllerName();
			$action		= $request->getActionName();
			if (strtolower($module . '_' . $controller . '_' . $action) != 'core_dashboard_index') {
				$this->_tracking = true;
			}
		}
	}
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown()
	{
		if (!$this->_tracking) {
			return;
		}
		$data = array();
		foreach ($this->_plugins as $plugin => $instance) {
			$data[$plugin] = $instance->getData();
		}
		
		// Store the data in the temp file
		$file = $this->_getFile();
		$data = "<?php return '" . addslashes(base64_encode(serialize($data))) . "';";
		file_put_contents($file, $data);
	}
	
	/**
	 * Called when installing this plugin
	 * 
	 * @return void
	 */
	public function install()
	{
		Core_Base_File::createDirectories('Core_Plugins_Debug_Plugin', APP_TEMP_DIR);
	}
	
	/**
	 * Called when uninstalling this plugin
	 * 
	 * @return void
	 */
	public function uninstall()
	{
		Core_Base_File::deleteDirectory(APP_TEMP_DIR . DS . 'Core_Plugins_Debug_Plugin');
	}
	
	/**
	 * Shows the menu item in the back-end
	 * 
	 * @return void
	 */
	public function menuAction()
	{
	}
	
	/**
	 * Shows the debug information
	 * 
	 * @return void
	 */
	public function debugAction()
	{
		Core_Services_Db::connect('master');
		
		$file = $this->_getFile();
		$data = null;
		if (file_exists($file)) {
			$data = include $file;
			$data = unserialize(base64_decode(stripslashes($data)));
		}
		
		$this->view->assign(array(
			'data'	   => $data,
			'plugins'  => Core_Services_Plugin::getOptionsByInstance($this, $this->_standardPlugins),
			'selected' => $this->_extension->getRequest()->getParam('selected', $this->_standardPlugins[0]),
		));
	}
	
	/**
	 * Shows the config form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$activePlugins = Core_Services_Plugin::getOptionsByInstance($this, $this->_standardPlugins);
		$plugins	   = array();
		foreach ($this->_standardPlugins as $plugin) {
			$plugins[$plugin] = array(
				'is_active' => in_array($plugin, $activePlugins),
			);
		}
		$this->view->assign('plugins', $plugins);
	}
	
	/**
	 * Saves the data posted from the config form
	 * 
	 * @return string "true" or "false"
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->_extension->getRequest();
		$plugins = $request->getParam('plugins');
		$result  = Core_Services_Plugin::setOptionsForInstance($this, $plugins);
		return $result ? 'true' : 'false';
	}
	
	/**
	 * Gets the path of file containing the debugging data
	 * 
	 * @return string
	 */
	private function _getFile()
	{
		$file = APP_TEMP_DIR . DS . 'Core_Plugins_Debug_Plugin';
		if (!file_exists($file)) {
			@mkdir($file);
		}
		return (Zend_Auth::getInstance()->hasIdentity()) 
				? $file . DS . Zend_Auth::getInstance()->getIdentity()->user_id . '.php'
				: $file . DS . 'data.php';
	}
}
