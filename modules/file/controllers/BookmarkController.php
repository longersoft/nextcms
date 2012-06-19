<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_BookmarkController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new bookmark
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$path	  = $request->getPost('path');
		$pathInfo = pathinfo($path);
		$bookmark = new File_Models_Bookmark(array(
						'connection_id' => $request->getPost('connection_id'),
						'name'			=> $pathInfo['basename'],
						'path'			=> $path,
					));
		$result   = File_Services_Bookmark::add($bookmark);
		$this->_helper->json(array(
			'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Removes bookmark
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$bookmark = new File_Models_Bookmark(array(
						'connection_id' => $request->getPost('connection_id'),
						'path'			=> $request->getPost('path'),
					));
		$result = File_Services_Bookmark::delete($bookmark);
		$this->_helper->json(array(
			'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Lists bookmarks
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getPost('connection_id');
		$bookmarks	  = File_Services_Bookmark::find($connectionId);
		$this->view->assign('bookmarks', $bookmarks);
	}
	
	/**
	 * Renames bookmark
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$bookmark = new File_Models_Bookmark(array(
							'bookmark_id' => $request->getPost('bookmark_id'),
							'name'		  => $request->getPost('name'),
						));
		$result   = File_Services_Bookmark::rename($bookmark);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
