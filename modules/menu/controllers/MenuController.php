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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Menu_MenuController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new menu
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$menuat	 = $request->getParam('format');
		
		switch ($menuat) {
			case 'json':
				$menu	= new Menu_Models_Menu(array(
					'title'		   => $request->getPost('title'),
					'description'  => $request->getPost('description'),
					'created_user' => Zend_Auth::getInstance()->getIdentity()->user_id,
					'created_date' => date('Y-m-d H:i:s'),
					'items'		   => Zend_Json::decode($request->getPost('items')),
					'language'	   => $request->getPost('language'),
					'translations' => $request->getPost('translations'),
				));
				$menuId = Menu_Services_Menu::add($menu);
				$result = true;
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$sourceId = $request->getParam('source_id');
				$source	  = $sourceId ? Menu_Services_Menu::getById($sourceId) : null;
				
				$this->view->assign(array(
					'source'	  => $source,
					'sourceItems' => $source ? Zend_Json::encode(Menu_Services_Menu::getItemsTreeData($source)) : null,
					'language'	  => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages'	  => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Deletes menu
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$menuId	 = $request->getParam('menu_id');
		$menu	 = Menu_Services_Menu::getById($menuId);
		
		switch ($format) {
			case 'json':
				$result = Menu_Services_Menu::delete($menu);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('menu', $menu);
				break;
		}
	}
	
	/**
	 * Edits menu
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$menuId	 = $request->getParam('menu_id');
		$menu	 = Menu_Services_Menu::getById($menuId);
		
		switch ($format) {
			case 'json':
				$result = false;
				if ($menu) {
					$menu->title	   = $request->getPost('title');
					$menu->description = $request->getPost('description');
					$menu->items	   = Zend_Json::decode($request->getPost('items'));
					
					// Update translation
					$menu->new_translations = $request->getPost('translations');
					if (!$menu->new_translations) {
						$menu->new_translations = Zend_Json::encode(array(
							$menu->language => (string) $menu->menu_id,
						));
					}
					
					$result = Menu_Services_Menu::update($menu);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				// Get the translations of the menu
				$translations = null;
				if ($menu) {
					$languages = Zend_Json::decode($menu->translations);
					unset($languages[$menu->language]);
					$translations = array();
					foreach ($languages as $locale => $id) {
						$translations[] = Menu_Services_Menu::getById($id);
					}
				}
				
				$this->view->assign(array(
					'menu'		   => $menu,
					'menuId'	   => $menuId,
					'items'		   => $menu ? Zend_Json::encode(Menu_Services_Menu::getItemsTreeData($menu)) : null,
					'languages'	   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'translations' => $translations,
				));
				break;
		}
	}
	
	/**
	 * Lists menus
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		$q		 = $request->getParam('q');
		$default = array(
			'page'	   => 1,
			'keyword'  => null,
			'per_page' => 20,
			'language' => null,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		
		switch ($format) {
			case 'json':
				$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
				$menus	  = Menu_Services_Menu::find($criteria, $offset, $criteria['per_page']);
				$total	  = Menu_Services_Menu::count($criteria);
				
				// Build data for the grid
				$items	 = array();
				$fields	 = array('menu_id', 'title', 'created_date', 'language', 'translations');
				foreach ($menus as $menu) {
					$item = array();
					foreach ($fields as $field) {
						$item[$field] = $menu->$field;
					}
					$items[] = $item;
				}
				
				// Paginator
				$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($menus, $total));
				$paginator->setCurrentPageNumber($criteria['page'])
						  ->setItemCountPerPage($criteria['per_page']);
				
				$data = array(
					// Data for the grid
					'data' => array(
						'identifier' => 'menu_id',
						'items'		 => $items,
					),
					// Paginator
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('/app/menu/menu/list/onGotoPage', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign('criteria', $criteria);
				break;
		}
	}
}
