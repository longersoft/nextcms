<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_CommentController extends Zend_Controller_Action
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
	 * Activates or deactivates comment
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$commentId = $request->getPost('comment_id');
		$comment   = Comment_Services_Comment::getById($commentId);
		if (!$comment) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$comment->status = $comment->status == Comment_Models_Comment::STATUS_ACTIVATED
								? Comment_Models_Comment::STATUS_NOT_ACTIVATED
								: Comment_Models_Comment::STATUS_ACTIVATED;
			$result = Comment_Services_Comment::updateStatus($comment);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}
	
	/**
	 * Deletes comment
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$commentId = $request->getParam('comment_id');
		$comment   = Comment_Services_Comment::getById($commentId);
		
		switch ($format) {
			case 'json':
				$result = Comment_Services_Comment::delete($comment);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('comment', $comment);
				break;
		}
	}
	
	/**
	 * Edits comment
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$commentId = $request->getParam('comment_id');
		$comment   = Comment_Services_Comment::getById($commentId);
		
		switch ($format) {
			case 'json':
				$result = false;
				if ($comment) {
					$comment->title		= $request->getPost('title');
					$comment->content	= $request->getPost('content');
					$comment->full_name = $request->getPost('full_name');
					$comment->web_site  = $request->getPost('web_site');
					$comment->email		= $request->getPost('email');
					
					$result = Comment_Services_Comment::update($comment);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'comment'   => $comment,
					'commentId' => $commentId,
				));
				break;
		}
	}
	
	/**
	 * Lists latest comments
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
			'status'   => Comment_Models_Comment::STATUS_NOT_ACTIVATED,
			'language' => Core_Services_Config::get('core', 'localization_default_language', 'en_US'),
			'counter'  => false,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		
		switch ($format) {
			case 'json':
				$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
				$comments = Comment_Services_Comment::find($criteria, $offset, $criteria['per_page']);
				$total	  = Comment_Services_Comment::count($criteria);
				
				// Build data for the grid
				$items	 = array();
				$fields	 = array('comment_id', 'entity_id', 'entity_class', 'entity_module', 'title', 'content', 'full_name', 'email', 'created_date');
				foreach ($comments as $comment) {
					$item = array();
					foreach ($fields as $field) {
						$item[$field] = $comment->$field;
					}
					// Show the first 100 characters of the content
					$item['short_content'] = $this->view->stringFormatter()->sub($item['content'], 100);
					$item['short_content'] = strip_tags($item['short_content']);
					
					$items[] = $item;
				}
				
				// Paginator
				$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($comments, $total));
				$paginator->setCurrentPageNumber($criteria['page'])
						  ->setItemCountPerPage($criteria['per_page']);
				
				$data = array(
					// Data for the grid
					'data' => array(
						'identifier' => 'comment_id',
						'items'		 => $items,
					),
					// Paginator
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('/app/comment/comment/list/onGotoPage', [__PAGE__]);"),
				);
				
				if ($criteria['counter']) {
					$counters = array(
						'total' => 0,
					);
					foreach (Comment_Models_Comment::$STATUS as $status) {
						$counters[$status] = Comment_Services_Comment::count(array(
												'status'   => $status,
												'language' => $criteria['language'],
											));
						$counters['total'] += $counters[$status];
					}
					$data['counters'] = $counters;
				}
				
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign('criteria', $criteria);
				break;
		}
	}
	
	/**
	 * Replies to given comment
	 * 
	 * @return void
	 */
	public function replyAction()
	{
		Core_Services_Db::connect('master');
		
		$request   = $this->getRequest();
		$format	   = $request->getParam('format');
		$commentId = $request->getParam('comment_id');
		$comment   = Comment_Services_Comment::getById($commentId);
		$user	   = Zend_Auth::getInstance()->getIdentity();
		
		switch ($format) {
			case 'json':
				$result = false;
				if ($comment) {
					$newComment = new Comment_Models_Comment(array(
						'entity_id'		=> $comment->entity_id,
						'entity_class'	=> $comment->entity_class,
						'entity_module' => $comment->entity_module,
						'title'			=> $request->getPost('title'),
						'content'		=> $request->getPost('content'),
						'full_name'		=> $request->getPost('full_name'),
						'web_site'		=> $request->getPost('web_site'),
						'email'			=> $request->getPost('email'),
						'ip'			=> $request->getClientIp(),
						'user_agent'	=> $request->getServer('HTTP_USER_AGENT'),
						'created_user'	=> $user->user_id,
						'created_date'	=> date('Y-m-d H:i:s'),
						'status'		=> Comment_Models_Comment::STATUS_NOT_ACTIVATED,
						'reply_to'		=> $comment->comment_id,
						'language'		=> $comment->language,
					));
					$newCommentId = Comment_Services_Comment::add($newComment);
					$result = true;
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'user'		=> $user,
					'comment'	=> $comment,
					'commentId' => $commentId,
					'quote'		=> $request->getParam('quote', 'false') == 'true',
				));
				break;
		}
	}
	
	/**
	 * Reports spam
	 * 
	 * @return void
	 */
	public function spamAction()
	{
		Core_Services_Db::connect('master');
		
		$this->view->headTitle()->set($this->view->translator()->_('comment.spam.title'));
		
		$request   = $this->getRequest();
		$commentId = $request->getPost('comment_id');
		$comment   = Comment_Services_Comment::getById($commentId);
		$result	   = Comment_Services_Comment::reportSpam($comment);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Views comment thread
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
			'page'			=> 1,
			'entity_id'		=> null,
			'entity_class'	=> null,
			'entity_module' => null,
			'keyword'		=> null,
			'per_page'		=> null,
			'sort_by'		=> 'ordering',
			'sort_dir'		=> 'ASC', 
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = $criteria['per_page'] ? (($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0) : 0;
		$comments = Comment_Services_Comment::find($criteria, $offset, $criteria['per_page']);
		$total	  = Comment_Services_Comment::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($comments, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		switch ($format) {
			case 'json':
				break;
			case 'html':
			default:
				$this->view->assign(array(
					'comments'	=> $comments,
					'total'		=> $total,
					'criteria'	=> $criteria,
					'paginator' => $paginator,
				));
				break;
		}
	}
}
