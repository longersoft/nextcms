<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_CategoryController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new category
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$parentId   = $request->getPost('parent_id');
				$parentId	= ($parentId == "") ? null : $parentId;
				$category 	= new Category_Models_Category(array(
									'parent_id'		   => $parentId,
									'left_id'		   => null,
									'right_id'		   => null,
									'user_id'		   => Zend_Auth::getInstance()->getIdentity()->user_id,
									'module'		   => $module,
									'name'			   => $request->getPost('name'),
									'slug'			   => $request->getPost('slug'),
									'image'			   => $request->getPost('image'),
									'meta_description' => $request->getPost('meta_description'),
									'meta_keyword'	   => $request->getPost('meta_keyword'),
									'language'		   => $request->getPost('language'),
									'translations'	   => $request->getPost('translations'),
								));
				$categoryId = Category_Services_Category::add($category);
				$result		= true;
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$sourceId = $request->getParam('source_id');
				
				$this->view->assign(array(
					'module'	=> $module,
					'parentId'  => $request->getParam('parent_id'),
					'source'	=> $sourceId ? Category_Services_Category::getById($sourceId) : null,
					'language'  => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Deletes category
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$categoryId	= $request->getParam('category_id');
		$format		= $request->getParam('format');
		$category	= Category_Services_Category::getById($categoryId);
		
		switch ($format) {
			case 'json':
				$result = Category_Services_Category::delete($category);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('category', $category);
				break;
		}
	}
	
	/**
	 * Edits category
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$categoryId	= $request->getParam('category_id');
		$format		= $request->getParam('format');
		$category	= Category_Services_Category::getById($categoryId);
		
		switch ($format) {
			case 'json':
				$category->name				= $request->getPost('name');
				$category->slug				= $request->getPost('slug');
				$category->image			= $request->getPost('image');
				$category->meta_description = $request->getPost('meta_description');
				$category->meta_keyword		= $request->getPost('meta_keyword');
				
				// Update translation
				$category->new_translations = $request->getPost('translations');
				if (!$category->new_translations) {
					$category->new_translations = Zend_Json::encode(array(
						$category->language => (string) $category->category_id,
					));
				}
				
				// Get parent category
				$parentId = $request->getPost('parent_id');
				$parent	  = ($category->parent_id) ? Category_Services_Category::getById($category->parent_id) : null;
				if ((null == $parent && $parentId == '') || ($parent != null && $parent->category_id == $parentId)) {
					$result	= Category_Services_Category::update($category);
				} else {
					$category->parent_id = $parentId;
					$result = Category_Services_Category::move($category);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				// Get the translations of the category
				$translations = null;
				if ($category) {
					$languages = Zend_Json::decode($category->translations);
					unset($languages[$category->language]);
					$translations = array();
					foreach ($languages as $locale => $id) {
						$translations[] = Category_Services_Category::getById($id);
					}
				}
				
				$this->view->assign(array(
					'category'	   => $category,
					'languages'	   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'translations' => $translations,
				));
				break;
		}
	}
	
	/**
	 * Lists categories
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$module   = $request->getParam('mod');
		$format	  = $request->getParam('format');
		$language = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		
		switch ($format) {
			case 'json':
				$data = Category_Services_Category::getTreeData($module, $language);
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign(array(
					'module'		  => $module,
					'language'		  => $language,
					'helperContainer' => $request->getParam('helper_container'),
				));
				break;
		}
	}
	
	/**
	 * Moves category
	 * 
	 * @return void
	 */
	public function moveAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$categoryId	= $request->getParam('category_id');
		$parentId	= $request->getParam('parent_id');
		$category	= Category_Services_Category::getById($categoryId);
		if (!$category || $category->parent_id == $parentId) {
			$result = false;
		} else {
			// To make translation work
			$category->new_translations = $category->translations;
			
			$category->parent_id = $parentId;
			$result = Category_Services_Category::move($category);
		}
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Renames category
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$categoryId	= $request->getParam('category_id');
		$format		= $request->getParam('format');
		$category	= Category_Services_Category::getById($categoryId);
		
		switch ($format) {
			case 'json':
				$category->name = $request->getPost('name');
				$category->slug = $request->getPost('slug');
				$result	= Category_Services_Category::rename($category);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('category', $category);
				break;
		}
	}
}
