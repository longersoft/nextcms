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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Hooks_Layout_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_core', array($this, 'menu'), true);
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/modules/core/hooks/layout/styles.css');
		}
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
	 * Show the layout toolbox
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('master');
		
		$this->view->assign('modules', Core_Services_Module::getInstalledModules());
	}
	
	/**
	 * Lists the widget based on the selected module
	 * 
	 * @return void
	 */
	public function widgetAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('selected_module');
		$widgets = Core_Services_Widget::find(array(
						'module' => $module,
					));
		
		$this->view->assign('widgets', $widgets);
	}
}
