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
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Defines the view scripts and view helper paths based on current template 
 */
class Core_Controllers_Plugins_Template extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		// Support template
		$template   = Core_Services_Template::getCurrentTemplate();
		$module		= $request->getModuleName();
		$controller = strtolower($request->getControllerName());
		$action		= strtolower($request->getActionName());	
		
		// Check if we are in modules or widgets folder
		$view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$file1 = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'views' . DS . 'scripts' . DS . $controller . DS . $action . '.phtml';
		$path  = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'views' . DS . $module . DS . 'views';
		$file2 = $path . DS . 'scripts' . DS . $controller . DS . $action . '.phtml';

		// Try to find the script in template first
		if (file_exists($file2)) {
			//$view->addScriptPath($path . DS . 'scripts' . DS);
			$view->setScriptPath($path . DS . 'scripts');

			// Add helper path for template
			if (file_exists($path . DS . 'helpers')) {
				$view->addHelperPath($path . DS . 'helpers', $module . '_View_Helper_');
				//$view->setHelperPath($path . DS . 'helpers', $module . '_View_Helper_');
			}
		}
	}
}
