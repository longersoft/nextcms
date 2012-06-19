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
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Controllers_Plugin extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Extension instance
	 * 
	 * @var Core_Base_Extension_Plugin
	 */
	protected $_extension;
	
	/**
	 * @var Zend_View_Abstract
	 */
	public $view;
	
	/**
	 * Name of module
	 * 
	 * @var string
	 */
	protected $_module;
	
	/**
	 * Name of plugin
	 * 
	 * @var string
	 */
	protected $_name;	
	
	public function __call($name, $arguments)
	{
		if ($this->_extension == null) {
			// Get the class's name. It has the format of ModuleName_Plugins_PluginName_Plugin
			$class = get_class($this);
			
			// Create the extension
			$paths = explode('_', $class);
			$this->_extension = new Core_Base_Extension_Plugin(strtolower($paths[2]), strtolower($paths[0]));
			$this->view		  = $this->_extension->view;
		}
		
		if (strlen($name) > 6 && substr($name, -6) == 'Action') {
			return;
		}
		
		$noRenderScript = $arguments && isset($arguments[0]['noRenderScript']) && $arguments[0]['noRenderScript'] == true;
		$actionName	    = $name . 'Action';
		if (method_exists($this, $actionName)) {
			if ($noRenderScript) {
				return $this->$actionName();
			} else {
				$this->$actionName();
			}
			return $this->_extension->__call($name, $arguments);
		}
	}
	
	/**
	 * Gets module name
	 * 
	 * @return string
	 */
	public function getModule()
	{
		if ($this->_module == null) {
			$paths 		   = explode('_', get_class($this));
			$this->_module = strtolower($paths[0]);
		}
		return $this->_module;
	}
	
	/**
	 * Gets plugin name
	 * 
	 * @return string
	 */
	public function getName()
	{
		if ($this->_name == null) {
			$paths		 = explode('_', get_class($this));
			$this->_name = strtolower($paths[2]);
		}
		return $this->_name;
	}
}
