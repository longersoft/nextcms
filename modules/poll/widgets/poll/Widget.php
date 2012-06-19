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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-24
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_Widgets_Poll_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows a form to select the poll
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$polls = array();
		$rows  = Poll_Services_Poll::find();
		
		// Get the list of localized languages
		$languages = array();
		$file = APP_ROOT_DIR . DS . 'data' . DS . 'l10n.php';
		if (file_exists($file)) {
			$languages = include_once $file;
		}
		
		foreach ($rows as $row) {
			if (!isset($polls[$row->language])) {
				$polls[$row->language] = array(
					'language' => isset($languages[$row->language]) ? $languages[$row->language]['native'] : $row->language,
					'polls'	   => array(),
				);
			}
			$polls[$row->language]['polls'][] = array(
				'poll_id' => $row->poll_id,
				'title'	  => $row->title,
			);
		}
		
		$request = $this->getRequest();
		$pollId  = $request->getParam('poll_id');
		$poll	 = $pollId ? Poll_Services_Poll::getById($pollId) : null;
		
		$this->view->assign(array(
			'poll'  => $poll,
			'polls' => $polls,
		));
	}
	
	/**
	 * Shows the results
	 * 
	 * @return void
	 */
	public function resultAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$pollId  = $request->getParam('poll_id');
		$answers = $request->getParam('answers');
		$poll	 = Poll_Services_Poll::getById($pollId);
		
		if ($answers) {
			// User just vote the poll
			$answers = explode(',', $answers);
			// Increase number of choices
			Core_Services_Counter::register($poll, 'votes', 'Poll_Services_Poll::increaseNumChoices', array($poll, $answers));
		}
		
		// Show the results
		$options = Poll_Services_Poll::getOptions($poll);
		$total	 = 0;
		if ($options) {
			foreach ($options as $option) {
				$total += $option->num_choices;
			}
		}
		
		$this->view->assign(array(
			'poll'	  => $poll,
			'options' => $options,
			'total'	  => $total,
		));
	}
	
	/**
	 * Shows the poll
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request = $this->getRequest();
		$pollId  = $request->getParam('poll_id');
		if ($pollId) {
			$poll	 = Poll_Services_Poll::getById($pollId);
			$voted	 = Core_Services_Counter::isRegistered($poll, 'votes');
			$options = Poll_Services_Poll::getOptions($poll);
			$total	 = 0;
			if ($options) {
				foreach ($options as $option) {
					$total += $option->num_choices;
				}
			}
			
			$this->view->assign(array(
				'poll'	  => $poll,
				'options' => $options,
				'voted'	  => $voted,
				'total'	  => $total,
			));
		}
	}
}
