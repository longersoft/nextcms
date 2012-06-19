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
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Controllers_Plugins_HookLoader extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		Core_Services_Db::connect('slave');
		
		$hooks = Core_Services_Hook::getInstalledHooks();
		if ($hooks) {
			foreach ($hooks as $h) {
				$hookClass = ucfirst(strtolower($h->module)) . '_Hooks_' . ucfirst(strtolower($h->name)) . '_Hook';
				if (class_exists($hookClass) && (($hook = new $hookClass()) instanceof Core_Base_Extension_Hook)) {
					Core_Services_Hook::registerInstance($hookClass, $hook);
				}
			}
		}
		
		$targets = Core_Services_Target::getTargets();
		foreach ($targets as $target) {
			$hookClass = ucfirst(strtolower($target->hook_module)) . '_Hooks_' . ucfirst(strtolower($target->hook_name)) . '_Hook';
			$hook = Core_Services_Hook::getRegisteredInstance($hookClass);
			
			if ($hook && ($hook instanceof Core_Base_Extension_Hook)) {
				Core_Base_Hook_Registry::getInstance()->register($target->target_name, array($hook, $target->hook_method), $target->echo == 1);
			}
		}
	}
}
