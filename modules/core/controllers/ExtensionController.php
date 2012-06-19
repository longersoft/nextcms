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
 * @version		2012-03-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_ExtensionController extends Zend_Controller_Action
{
	/**
	 * Renders output of an extension's action
	 * 
	 * @return void
	 */
	public function renderAction()
	{
		Zend_Layout::getMvcInstance()->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		$type    = $request->getParam('_type', 'widget');
		$module  = $request->getParam('_mod');
		$name	 = $request->getParam('_name');
		$method  = $request->getParam('_method');
		$format  = $request->getParam('_format');
		
		$class = '';
		switch ($type) {
			case 'plugin':
				$class = ucfirst(strtolower($module)) . '_Plugins_' . ucfirst(strtolower($name)) . '_Plugin';
				break;
			case 'hook':
				$class = ucfirst(strtolower($module)) . '_Hooks_' . ucfirst(strtolower($name)) . '_Hook';
				break;
			case 'task':
				$class = ucfirst(strtolower($module)) . '_Tasks_' . ucfirst(strtolower($name)) . '_Task';
				break;
			case 'widget':
			default:
				$class = ucfirst(strtolower($module)) . '_Widgets_' . ucfirst(strtolower($name)) . '_Widget';
				break;
		}
		
		if ($class == '' || !class_exists($class)) {
			$this->getResponse()->setBody('');
		} else {
			$extension = new $class();
			$output	   = '';
			
			// Check if user has permission to perform the action or not
			$resource  = strtolower($type . ':' . $module . ':' . $name);
			$isAllowed = !Core_Services_Acl::getInstance()->has($resource)
						? true		// The ACL does not have the resource
						: Core_Services_Acl::getInstance()->isUserOrRoleAllowed($module, $name, $method, $type);
			if ($isAllowed) {
				$output = $extension->$method();
			} else {
				$output = $this->view->translator()->_('auth.deny.notAllowed');
			}
			
			switch ($format) {
				case 'json':
					$this->_helper->json($output);
					break;
				default:
					$this->getResponse()->setBody($output);
					break;
			}
		}
	}
}
