<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_PollController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Adds new poll
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$poll = new Poll_Models_Poll(array(
					'title'			   => $request->getPost('title'),
					'description'	   => $request->getPost('description'),
					'created_user'	   => Zend_Auth::getInstance()->getIdentity()->user_id,
					'created_date'	   => date('Y-m-d H:i:s'),
					'multiple_options' => ($request->getPost('multiple_options') == 'on') ? 1 : 0,
					'language'		   => $request->getPost('language'),
					'translations'	   => $request->getPost('translations'),
					'options'		   => $request->getPost('options'),
				));
				$pollId = Poll_Services_Poll::add($poll);
				$result = true;
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$sourceId = $request->getParam('source_id');
				$source   = $sourceId ? Poll_Services_Poll::getById($sourceId) : null;
				
				$this->view->assign(array(
					'source'		=> $source,
					'sourceOptions' => $source ? Poll_Services_Poll::getOptions($source) : null,
					'language'		=> $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages'		=> Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Deletes poll
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$pollId	 = $request->getParam('poll_id');
		$poll	 = Poll_Services_Poll::getById($pollId);
		
		switch ($format) {
			case 'json':
				$result = Poll_Services_Poll::delete($poll);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('poll', $poll);
				break;
		}
	}
	
	/**
	 * Edits poll
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$pollId	 = $request->getParam('poll_id');
		$poll	 = Poll_Services_Poll::getById($pollId);
		
		switch ($format) {
			case 'json':
				$result = false;
				if ($poll) {
					$poll->title			= $request->getPost('title');
					$poll->description		= $request->getPost('description');
					$poll->multiple_options = ($request->getPost('multiple_options') == 'on') ? 1 : 0;
					
					$optionsArray = array();
					if ($options = $request->getPost('options')) {
						$numChoices = $request->getPost('num_choices');
						
						foreach ($options as $index => $option) {
							$optionsArray[] = new Poll_Models_Option(array(
								'poll_id'	  => $poll->poll_id,
								'ordering'	  => $index,
								'title'		  => $option,
								'num_choices' => $numChoices[$index],
							));
						}
					}
					$poll->options = $optionsArray;
					
					// Update translation
					$poll->new_translations = $request->getPost('translations');
					if (!$poll->new_translations) {
						$poll->new_translations = Zend_Json::encode(array(
							$poll->language => (string) $poll->poll_id,
						));
					}
					
					$result = Poll_Services_Poll::update($poll);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				// Get the translations of the poll
				$translations = null;
				if ($poll) {
					$languages = Zend_Json::decode($poll->translations);
					unset($languages[$poll->language]);
					$translations = array();
					foreach ($languages as $locale => $id) {
						$translations[] = Poll_Services_Poll::getById($id);
					}
				}
				
				$this->view->assign(array(
					'poll'		   => $poll,
					'pollId'	   => $pollId,
					'options'	   => Poll_Services_Poll::getOptions($poll),
					'languages'	   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'translations' => $translations,
				));
				break;
		}
	}
	
	/**
	 * Lists polls
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
			'language' => null,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		
		switch ($format) {
			case 'json':
				$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
				$polls	  = Poll_Services_Poll::find($criteria, $offset, $criteria['per_page']);
				$total	  = Poll_Services_Poll::count($criteria);
				
				// Build data for the grid
				$items	 = array();
				$fields	 = array('poll_id', 'title', 'created_date', 'language', 'translations');
				foreach ($polls as $poll) {
					$item = array();
					foreach ($fields as $field) {
						$item[$field] = $poll->$field;
					}
					$items[] = $item;
				}
				
				// Paginator
				$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($polls, $total));
				$paginator->setCurrentPageNumber($criteria['page'])
						  ->setItemCountPerPage($criteria['per_page']);
				
				$data = array(
					// Data for the grid
					'data' => array(
						'identifier' => 'poll_id',
						'items'		 => $items,
					),
					// Paginator
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('/app/poll/poll/list/onGotoPage', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign('criteria', $criteria);
				break;
		}
	}
}
