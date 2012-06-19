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
 * @subpackage	application
 * @since		1.0
 * @version		2012-02-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * This resource only loads modules which are installed, not all modules 
 * found in the "modules" directory.
 */
class Core_Application_Resource_Modules extends Zend_Application_Resource_Modules
{
	/**
	 * @see Zend_Application_Resource_Modules::bootstrapBootstraps()
	 */
	protected function bootstrapBootstraps($bootstraps)
	{
		$bootstrap = $this->getBootstrap();
		$out = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

		// Get the list of installed modules
		$modules = Core_Services_Module::getBootstrapModules();

		foreach ($modules as $module) {
			if (isset($bootstraps[$module])) {
				$bootstrapClass  = $bootstraps[$module];
				$moduleBootstrap = new $bootstrapClass($bootstrap);
				$moduleBootstrap->bootstrap();
				$out[$module] = $moduleBootstrap;
			}
		}

		return $out;
	}
}
