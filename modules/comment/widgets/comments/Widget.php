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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_Widgets_Comments_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Sends new comment
	 * 
	 * @return void
	 */
	public function sendAction()
	{
		Core_Services_Db::connect('master');
		
		$result		 = true;
		$request	 = $this->getRequest();
		$entityClass = $request->getParam('entity_class', '');
		
		$comment	 = new Comment_Models_Comment(array(
			'entity_id'		=> $request->getParam('entity_id'),
			'entity_class'	=> $entityClass,
			'title'			=> $request->getParam('title'),
			'content'		=> $request->getParam('content'),
			'full_name'		=> $request->getParam('full_name'),
			'web_site'		=> $request->getParam('web_site'),
			'email'			=> $request->getParam('email'),
			'ip'			=> $request->getClientIp(),
			'user_agent'	=> $request->getServer('HTTP_USER_AGENT'),
			'created_user'	=> Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->user_id : null,
			'created_date'	=> date('Y-m-d H:i:s'),
			'status'		=> Comment_Models_Comment::STATUS_NOT_ACTIVATED,
			'reply_to'		=> $request->getParam('reply_to', null),
			'language'		=> Zend_Controller_Front::getInstance()->getRequest()->getParam('lang'),
		));
		
		if ('true' == Core_Services_Config::get('comment', 'auth_required', 'false') && !Zend_Auth::getInstance()->hasIdentity()) {
			$result = false;
		} elseif (empty($entityClass) || empty($comment->entity_id) || empty($comment->content) || empty($comment->email)) {
			$result = false;
		} else {
			$request	  = $this->getRequest();
			$entityClass  = $request->getParam('entity_class');
			$clazz		  = explode('_', $entityClass);
			
			$comment->entity_module = $clazz[0];
			Comment_Services_Comment::add($comment);
			$result = true;
		}
		
		return array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		);
	}
	
	/**
	 * Shows the comments
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request	  = $this->getRequest();
		$entityId	  = $request->getParam('entity_id');
		$entityClass  = $request->getParam('entity_class');
		if ($entityClass) {
			$clazz		  = explode('_', $entityClass);
			$entityModule = $clazz[0];
		} else {
			$entityModule = '';
		}
		
		$perPage  = 20;
		$page	  = $request->getParam('page', 1);
		$criteria = array(
			'entity_id'		=> $entityId,
			'entity_class'  => $entityClass,
			'entity_module' => $entityModule,
			'status'		=> Comment_Models_Comment::STATUS_ACTIVATED,
			'sort_by'		=> 'ordering',
			'sort_dir'	    => 'ASC',
		);
		$comments = Comment_Services_Comment::find($criteria, ($page - 1) * $perPage, $perPage);
		$total	  = Comment_Services_Comment::count($criteria);
		
		// Pager
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($comments, $total));
		$paginator->setCurrentPageNumber($page)
				  ->setItemCountPerPage($perPage);
				  
		$timeDiffFormats = array(
			'DAY'			=> $this->view->translator()->_('show.daysAgo'),
			'DAY_HOUR'		=> $this->view->translator()->_('show.daysHoursAgo'),
			'HOUR'			=> $this->view->translator()->_('show.hoursAgo'),
			'HOUR_MINUTE'	=> $this->view->translator()->_('show.hoursMinutesAgo'),
			'MINUTE'		=> $this->view->translator()->_('show.minutesAgo'),
			'MINUTE_SECOND'	=> $this->view->translator()->_('show.minutesSecondsAgo'),
			'SECOND'		=> $this->view->translator()->_('show.secondsAgo'),
		);
		
		$this->view->assign(array(
			'entityId'		  => $entityId,
			'entityClass'	  => $entityClass,
			'comments'	  	  => $comments,
			'total'		  	  => $total,
			'paginator'		  => $paginator,
			'timeDiffFormats' => $timeDiffFormats,
			'container'		  => $request->getParam('container', uniqid('commentWidgetsComments_')),
		));
	}
}
