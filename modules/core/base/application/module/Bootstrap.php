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
 * @version		2011-12-20
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Based on module bootstrapping article of Chris Woodford 
 * @see http://offshootinc.com/blog/2011/02/11/modul-bootstrapping-in-zend-framework/
 */
class Core_Base_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function __construct($application)
	{
		parent::__construct($application);
		// $this->_loadModuleConfig();
		$this->_loadInitializer();
	}
	
	/**
	 * Loads a module specific config file
	 * 
	 * @return void
	 */
	protected function _loadModuleConfig()
	{
		$configFile = APP_ROOT_DIR . DS . 'modules'
					. DS . strtolower($this->getModuleName())
					. DS . 'configs' . DS . 'module.php';
		if (!file_exists($configFile)) {
			return;
		}
		$config = include $configFile;
		$this->setOptions($config);
	}
	
	/**
	 * Adds the bootstrap intializer to the resource loader
	 * 
	 * @return void
	 */
	protected function _loadInitializer()
	{
		$this->getResourceLoader()->addResourceType(
			'Bootstrap_Initializer', 'bootstrap', 'Bootstrap'
		);
	}
}
