<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Menu_Widgets_Menu_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows a form to select the menu
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$menus = Menu_Services_Menu::find();
		$this->view->assign('menus', $menus);
	}
	
	/**
	 * Shows the menu
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request = $this->getRequest();
		$menuId  = $request->getParam('menu_id');
		
		// ZF LESSON: Use Zend_Navigation to build the menu structure
		$container = new Zend_Navigation();
		
		if ($menuId) {
			$menu  = Menu_Services_Menu::getById($menuId);
			$items = Menu_Services_Menu::getItemsTree($menu);
			
			foreach ($items as $item) {
				$properties = array(
					'label'		=> $item->title,
					'uri'		=> $this->view->urlHelper()->normalizeUrl($item->link),
					'_identify' => $item->item_id,
				);
				if ($item->html_id) {
					$properties['id'] = $item->html_id;
				}
				if ('_blank' == $item->target) {
					$properties['target'] = '_blank';
				}
				if ($item->css_class) {
					$properties['class'] = $item->css_class;
				}
				
				$page = new Zend_Navigation_Page_Uri($properties);
				
				if ($item->parent_id == 0) {
					$container->addPage($page);
				} else {
					$page->setParent($container->findOneBy('_identify', $item->parent_id));
				}
			}
		}
		
		$this->view->assign('container', $container);
	}
}
