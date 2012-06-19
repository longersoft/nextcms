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
class Core_Base_Application_Module_Initializer extends Zend_Application_Bootstrap_BootstrapAbstract
{
	/**
	 * @var Core_Base_Application_Module_Bootstrap
	 */
	protected $_bootstrap;

	/**
	 * Initializes the intializer
	 * 
	 * @param Core_Base_Application_Module_Bootstrap $bootstrap
	 * @throws Zend_Application_Bootstrap_Exception
	 */
	public function __construct($bootstrap)
	{
		if (!$bootstrap instanceof Core_Base_Application_Module_Bootstrap) {
			throw new Zend_Application_Bootstrap_Exception(
				__CLASS__ . '::__construct expects an instance of Core_Base_Application_Module_Bootstrap'
			);
		}

		$this->_bootstrap = $bootstrap;
	}

	/**
	 * Not used but required by interface
	 * 
	 * @see Zend_Application_Bootstrap_Bootstrapper::run()
	 */
	public function run()
	{
	}

	/**
	 * Gets the bootstrap object that is for the module being initialized
	 * 
	 * @return Core_Base_Application_Module_Bootstrap
	 */
	public function getBootstrap()
	{
		return $this->_bootstrap;
	}

	/**
	 * Bootstrap individual, all, or multiple resources
	 *
	 * @param  null|string|array $resource
	 * @return Core_Base_Application_Module_Initializer
	 * @throws Zend_Application_Bootstrap_Exception
	 */
	final public function initialize($resource = null)
	{
		$this->_bootstrap($resource);
		return $this;
	}
}
