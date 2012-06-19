<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_FolderController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds an article to a folder
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$folderId  = $request->getPost('folder_id');
		$articleId = $request->getPost('article_id');
		
		$result	   = Content_Services_Article::addToFolder($articleId, $folderId);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Removes an article from a folder
	 * 
	 * @return void
	 */
	public function removeAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$folderId  = $request->getPost('folder_id');
		$articleId = $request->getPost('article_id');
		
		$result	   = Content_Services_Article::removeFromFolder($articleId, $folderId);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
