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

class Message_FilterController extends Zend_Controller_Action
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
	 * Adds new filter
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		
		// Build the filter action
		$actions = array();
		foreach (Message_Models_Filter::$ACTIONS as $action) {
			$actions[$action] = 0;
		}
		foreach ($request->getPost('actions', array()) as $key => $value) {
			$actions[$value] = 1;
		}
		$folderId = $request->getPost('folder_id', null);
		$actions[Message_Models_Filter::ACTION_MOVE] = $folderId;
		
		$filter = new Message_Models_Filter(array(
			'user_id'	    => Zend_Auth::getInstance()->getIdentity()->user_id,
			'object'	    => $request->getPost('object'),
			'condition'	    => $request->getPost('condition'),
			'comparison_to' => $request->getPost('comparison_to'),
			'actions'	    => Zend_Json::encode($actions),
			'folder_id'	    => $folderId,
		));
		$filterId = Message_Services_Filter::add($filter);
		
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Deletes given filter
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$format	  = $request->getParam('format');
		$filterId = $request->getParam('filter_id');
		$filter	  = Message_Services_Filter::getById($filterId);
		
		// Don't allow user to delete filter created by other users
		if ($filter == null || $filter->user_id != Zend_Auth::getInstance()->getIdentity()->user_id) {
			throw new Exception('You cannot delete message filter of other users');
		}
		
		$result = Message_Services_Filter::delete($filter);
		$this->_helper->json(array(
			'result'	=> $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'filter_id' => $filterId,
		));
	}
	
	/**
	 * Lists filters
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$user	 = Zend_Auth::getInstance()->getIdentity();
		
		switch ($format) {
			case 'html':
				$filters = Message_Services_Filter::find(array(
								'user_id'  => $user->user_id,
							));
				$this->view->assign('filters', $filters);
				break;
			default:
				// Get the list of folders
				$folders = Message_Services_Folder::find(array(
								'user_id'  => $user->user_id,
								'sort_dir' => 'ASC', 
							));
				$this->view->assign('folders', $folders);
				break;
		}
	}
}
