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
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_WidgetController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////

	/**
	 * Renders a widget
	 * 
	 * @return void
	 */
	public function renderAction()
	{
		Zend_Layout::getMvcInstance()->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		$module  = $request->getParam('_mod');
		$name	 = $request->getParam('_name');
		$method  = $request->getParam('_method');
		$format  = $request->getParam('_format');
		
		$class	 = ucfirst(strtolower($module)) . '_Widgets_' . ucfirst(strtolower($name)) . '_Widget';
		if ($class == '' || !class_exists($class)) {
			$this->getResponse()->setBody('');
		} else {
			$widget = new $class();
			$output	= '';
			// Check if user has permission to perform the action or not
			$resource  = strtolower('widget:' . $module . ':' . $name);
			$isAllowed = !Core_Services_Acl::getInstance()->has($resource)
						 ? true
						 : Core_Services_Acl::getInstance()->isUserOrRoleAllowed($module, $name, $method, 'widget');
			if ($isAllowed) {
				$output = $widget->$method();
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
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Installs a widget
	 * 
	 * @return void
	 */
	public function installAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Widget::install($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Lists plugins
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
				
		$installedWidgets = array();
		foreach (Core_Services_Widget::find(array('module' => $module)) as $widget) {
			$installedWidgets[] = $widget->module . '_' . $widget->name;
		}
		
		$widgets = array();
		$dirs	 = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'widgets');
		foreach ($dirs as $dir) {
			$widget = Core_Services_Widget::getWidgetInstance($dir, $module);
			if ($widget) {
				$widget->thumbnail	  = $this->view->APP_ROOT_URL . '/' . ltrim($widget->thumbnail, '/');
				$widget->is_installed = in_array($widget->module . '_' . $widget->name, $installedWidgets);
				
				$widgets[] = $widget;
			}
		}
		
		$this->view->assign('widgets', $widgets);
	}
	
	/**
	 * Uninstalls a widget
	 * 
	 * @return void
	 */
	public function uninstallAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Widget::uninstall($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
