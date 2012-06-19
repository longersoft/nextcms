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
 * @version		2012-03-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_FolderController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new folder
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request	 = $this->getRequest();
		$entityClass = $request->getParam('entity_class');
		$format	  	 = $request->getParam('format');
		$language	 = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		
		switch ($format) {
			case 'json':
				$folder = new Category_Models_Folder(array(
					'user_id'	   => Zend_Auth::getInstance()->getIdentity()->user_id,
					'entity_class' => $entityClass,
					'name'		   => $request->getParam('name'),
					'language'	   => $language,
				));
				Category_Services_Folder::add($folder);
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'entityClass' => $entityClass,
					'language'	  => $language,
				));
				break;
		}
	}
	
	/**
	 * Deletes a folder
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$folderId = $request->getParam('folder_id');
		$format	  = $request->getParam('format');
		$folder	  = $folderId ? Category_Services_Folder::getById($folderId) : null;
		
		switch ($format) {
			case 'json':
				$result = Category_Services_Folder::delete($folder);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('folder', $folder);
				break;
		}
	}
	
	/**
	 * Lists folders
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request	 = $this->getRequest();
		$entityClass = $request->getParam('entity_class');
		$format	  	 = $request->getParam('format');
		$language	 = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$criteria	 = array(
			'entity_class' => $entityClass,
			'language'	   => $language,
		);
		
		$this->view->assign(array(
			'entityClass'	  => $entityClass,
			'language'		  => $language,
			'helperContainer' => $request->getParam('helper_container'),
			'folders'		  => Category_Services_Folder::find($criteria),
		));
	}
	
	/**
	 * Renames a folder
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$folderId = $request->getParam('folder_id');
		$name	  = $request->getParam('name');
		$folder	  = $folderId ? Category_Services_Folder::getById($folderId) : null;
		
		$result   = false;
		if ($folder) {
			$folder->name = $name;
			$result	= Category_Services_Folder::rename($folder);
		}
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'name'   => $name,
		));
	}
}
