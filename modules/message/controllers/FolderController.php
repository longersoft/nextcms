<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_FolderController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Adds new folder
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format'); 
		
		switch ($format) {
			case 'json':
				$folder   = new Message_Models_Folder(array(
								'name'	  => $request->getPost('name'),
								'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
							));
				$folderId = Message_Services_Folder::add($folder);
				$this->_helper->json(array(
					'result'	=> 'APP_RESULT_OK',
					'folder_id' => $folderId,
					'name'		=> $folder->name,
				));
				break;
			default:
				break;
		}
	}
	
	/**
	 * Deletes folder
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$format	  = $request->getParam('format');
		$folderId = $request->getParam('folder_id');
		$folder	  = Message_Services_Folder::getById($folderId);
		
		// Don't allow user to delete folder of other users
		if ($folder == null || $folder->user_id != Zend_Auth::getInstance()->getIdentity()->user_id) {
			throw new Exception('You cannot delete message folder of other users');
		}
		
		switch ($format) {
			case 'json':
				$result = Message_Services_Folder::delete($folder);
				$this->_helper->json(array(
					'result'	=> $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'folder_id' => $folderId,
					'name'		=> $folder->name,
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
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$q		 = $request->getParam('q');
		$default = array(
			'sort_dir' => 'ASC',
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$criteria['user_id'] = Zend_Auth::getInstance()->getIdentity()->user_id;
		
		$folders = Message_Services_Folder::find($criteria);
		
		switch ($format) {
			case 'json':
				$items = array();
				foreach ($folders as $folder) {
					$items[] = $folder->getProperties();
				}
				$this->_helper->json($items);
				break;
			default:
				$this->view->assign('folders', $folders);
				break;
		}
	}
	
	/**
	 * Renames folder
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$format	  = $request->getParam('format');
		$folderId = $request->getParam('folder_id');
		$folder	  = Message_Services_Folder::getById($folderId);
		
		// Don't allow user to rename folder of other users
		if ($folder == null || $folder->user_id != Zend_Auth::getInstance()->getIdentity()->user_id) {
			throw new Exception('You cannot rename message folder of other users');
		}

		$folder->name = $request->getParam('name');
		$result = Message_Services_Folder::rename($folder);
		$this->_helper->json(array(
			'result'	=> $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'folder_id' => $folderId,
			'name'		=> $folder->name,
		));
	}
}
