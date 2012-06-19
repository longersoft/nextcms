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
 * @version		2012-01-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_RevisionController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Deletes revision
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$format		= $request->getParam('format');
		$revisionId = $request->getParam('revision_id');
		
		$revision = Content_Services_Revision::getById($revisionId);
		switch ($format) {
			case 'json':
				$result = Content_Services_Revision::delete($revision);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('revision', $revision);
				break;
		}
	}
	
	/**
	 * Lists revisions
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$articleId = $request->getParam('article_id');
		$format	   = $request->getParam('format');
		$article   = Content_Services_Article::getById($articleId);
		
		switch ($format) {
			case 'json':
				$keyword   = $request->getParam('keyword');
				$revisions = Content_Services_Revision::find(array('article_id' => $articleId, 'keyword' => $keyword));
				$items	   = array();
				$fields	   = array('revision_id', 'comment', 'is_active', 'versioning_date', 'article_id', 'title');
				
				foreach ($revisions as $revision) {
					$item = array();
					foreach ($fields as $field) {
						$item[$field] = $revision->$field;
					}
					$items[] = $item;
				}
				$data = array(
					'identifier' => 'revision_id',
					'items'		 => $items,
				);
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign('article', $article);
				break;
		}
	}
	
	/**
	 * Restores revision
	 * 
	 * @return void
	 */
	public function restoreAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$format		= $request->getParam('format');
		$revisionId = $request->getParam('revision_id');
		
		$revision = Content_Services_Revision::getById($revisionId);
		switch ($format) {
			case 'json':
				$result = Content_Services_Revision::restore($revision);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('revision', $revision);
				break;
		}
	}
	
	/**
	 * Views revision details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$revisionId = $request->getParam('revision_id');
		$revision	= Content_Services_Revision::getById($revisionId);
		
		// Get article's tags
		$tags		= array();
		if ($revision->tags) {
			foreach (explode(',', $revision->tags) as $tagId) {
				if ($tag = Tag_Services_Tag::getById($tagId)) {
					$tags[] = $tag;
				}
			}
		}
		
		$article	= new Content_Models_Article($revision->getProperties());
		$this->view->assign(array(
			'revision' => $revision,
			'tags'	   => $tags,
		));
	}
}
