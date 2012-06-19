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
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_MessageController extends Zend_Controller_Action
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
		$ajaxContext->addActionContext('view', 'html')
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Deletes message
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$messageId = $request->getParam('message_id');
		$deleted   = $request->getParam('deleted', '0');
		$message   = Message_Services_Message::getById($messageId);
		$user	   = Zend_Auth::getInstance()->getIdentity();
		
		switch ($format) {
			case 'json':
				$result = Message_Services_Message::delete($message, $user);
				$this->_helper->json(array(
					'result'	 => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'message_id' => $messageId,
					'deleted'	 => ($result && $deleted == '1') ? '1' : '0',
				));
				break;
			default:
				$this->view->assign(array(
					'message' => $message,
					'deleted' => $deleted,
				));
				break;
		}
	}
	
	/**
	 * Empties the trash
	 * 
	 * @return void
	 */
	public function emptyAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$user	= Zend_Auth::getInstance()->getIdentity();
				$result = Message_Services_Message::emptyTrash($user);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				break;
		}
	}
	
	/**
	 * Lists user's private messages
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		$q		 = $request->getParam('q');
		$default = array(
			'folder_id' => Message_Models_Folder::FOLDER_INBOX,
			'starred'	=> null,
			'deleted'	=> '0',
			'keyword'   => null,
			'page'		=> 1,
			'per_page'	=> 20,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$criteria['user_id'] = Zend_Auth::getInstance()->getIdentity()->user_id;
		
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$messages = Message_Services_Message::findThreads($criteria, $offset, $criteria['per_page']);
		$total	  = Message_Services_Message::countThreads($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($messages, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
				  
		switch ($format) {
			case 'json':
				// Build data for the grid
				$paginatorTopic = $request->getParam('topic', '/app/message/message/list/onGotoPage');
				
				$items = array();
				foreach ($messages as $message) {
					$properties = $message->getProperties();
					
					// Show the total of messages in each thread
					$properties['subject'] .= ' (' . $message->num_messages . ')';
					
					// Show the first 100 characters of the content
					$properties['short_content'] = $this->view->stringFormatter()->sub($message->content, 100);
					$properties['short_content'] = strip_tags($properties['short_content']);
					
					// Add a property named "has_attachments" to indicate the message has attachments or not
					$properties['has_attachments'] = ($message->attachments != null && !empty($message->attachments));
					
					$items[] = $properties;
				}
				$data = array(
					'messages'	=> array(
						'identifier' => 'message_id',
						'items'		 => $items,
					),
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('" . $paginatorTopic . "', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign('criteria', $criteria);
				break;
		}
	}
	
	/**
	 * Marks message as read/unread
	 * 
	 * @return void
	 */
	public function markAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$messageId = $request->getParam('message_id');
		$message   = Message_Services_Message::getById($messageId);
		$user      = Zend_Auth::getInstance()->getIdentity();
		
		$result    = Message_Services_Message::toggleRead($message, $user);
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Moves message to other folder
	 * 
	 * @return void
	 */
	public function moveAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$messageId = $request->getParam('message_id');
		$folderId  = $request->getParam('folder_id');
		
		$user      = Zend_Auth::getInstance()->getIdentity();
		$message   = Message_Services_Message::getById($messageId);
		
		if ($folderId == "inbox") {
			// It is special case
			// The app allows to move message to the "Inbox" folder
			$folder = new Message_Models_Folder(array(
				'folder_id' => 'inbox',
				'user_id'	=> $user->user_id,
			));
		} else {
			$folder	= Message_Services_Folder::getById($folderId);
		}
		
		$result	= Message_Services_Message::move($message, $user, $folder);
		$this->_helper->json(array(
			'result'	=> $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'folder_id' => $folderId,
		));
	}
	
	/**
	 * Sends new or replies given private message
	 * 
	 * @return void
	 */
	public function sendAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$messageId = $request->getParam('message_id');
		$message   = $messageId ? Message_Services_Message::getById($messageId) : null;
		
		switch ($format) {
			case 'json':
				$newMessage = new Message_Models_Message(array(
					'root_id'	   => $message ? $message->root_id : 0,
					'sent_user'	   => Zend_Auth::getInstance()->getIdentity()->user_id,
					'subject'	   => $request->getPost('subject'),
					'content'	   => $request->getPost('content'),
					'sent_date'	   => date('Y-m-d H:i:s'),
					'reply_to'	   => $messageId ? $messageId : 0,
					'to_address'   => $request->getPost('to_address'),
					'bcc_address'  => $request->getPost('bcc_address'),
					'attachments'  => $request->getPost('attachments'),
				));
				Message_Services_Message::add($newMessage);
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$replyAll	= $request->getParam('reply_all', 'false') == 'true';
				$recipients = array();
				if ($message) {
					$recipients = $replyAll ? array_merge(array($message->sent_user), explode(',', $message->to_address)) : $message->sent_user;
					$recipients = Message_Services_Message::getRecipients($recipients);
				}
				
				$this->view->assign(array(
					'containerId' => $request->getParam('container_id'),
					'message'	  => $message,
					'recipients'  => $recipients,
				));
				break;
		}
	}
	
	/**
	 * Add star or removes star to message
	 * 
	 * @return void
	 */
	public function starAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$messageId = $request->getParam('message_id');
		$message   = Message_Services_Message::getById($messageId);
		$user      = Zend_Auth::getInstance()->getIdentity();
		
		$result    = Message_Services_Message::toggleStar($message, $user);
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Views private messages in a thread
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$q		 = $request->getParam('q');
		$default = array(
			'page'	   => 1,
			'deleted'  => '0',		// By default, the deleted messages will not be listed in the thread
			'keyword'  => null,
			'per_page' => null,
			'sort_dir' => 'ASC',
		);
		
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$criteria['user_id'] = Zend_Auth::getInstance()->getIdentity()->user_id;
		
		$offset	  = $criteria['per_page'] ? (($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0) : 0;
		$messages = Message_Services_Message::find($criteria, $offset, $criteria['per_page']);
		$total	  = Message_Services_Message::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($messages, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		switch ($format) {
			case 'json':
				break;
			case 'html':
			default:
				$this->view->assign(array(
					'messages'	=> $messages,
					'total'		=> $total,
					'criteria'	=> $criteria,
					'paginator' => $paginator,
				));
				break;
		}
	}
}
