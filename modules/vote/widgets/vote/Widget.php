<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		vote
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Vote_Widgets_Vote_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Shows the like/dislike buttons
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request	 = $this->getRequest();
		$entity		 = $request->getParam('entity');
		if ($entity && ($entity instanceof Core_Base_Models_Entity)) {
			$entity = array(
				'entity_id'    => $entity->getId(),
				'entity_class' => get_class($entity),
			);
		} else {
			$entity = array(
				'entity_id'    => $request->getParam('entity_id'),
				'entity_class' => $request->getParam('entity_class'),
			);
		}
		
		$criteria = array(
			'entity_id'    => $entity['entity_id'],
			'entity_class' => $entity['entity_class'],
			'vote'		   => $request->getParam('vote', 1),
			'user_id'	   => Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->user_id : null,
			'ip'		   => $request->getClientIp(),
		);
		
		$numUps   = $request->getParam('num_ups');
		$numDowns = $request->getParam('num_downs');
		if ($numUps == '' || $numDowns == '') {
			$votes	  = Vote_Services_Vote::getNumVotes($entity);
			$numUps	  = $votes['num_ups'];
			$numDowns = $votes['num_downs'];
		}
		
		$this->view->assign(array(
			'entity'		=> $entity,
			'encodedEntity' => $this->view->encoder()->encode($entity),
			'voted'		    => Vote_Services_Vote::count($criteria) > 0,
			'numUps'		=> $numUps,
			'numDowns'		=> $numDowns,
			'authRequired'  => (Core_Services_Config::get('vote', 'auth_required', 'false') == 'true') && Zend_Auth::getInstance()->hasIdentity() == false,
		));
	}
	
	/**
	 * Ratings given entity
	 * 
	 * @return array
	 */
	public function voteAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$entity	  = $request->getParam('entity');
		$result   = false;
		$upOrDown = $request->getParam('vote', 1);
		
		if ($entity) {
			$entity = $this->view->encoder()->decode($entity);
			$vote   = new Vote_Models_Vote(array(
				'entity_id'    => $entity['entity_id'],
				'entity_class' => $entity['entity_class'],
				'vote'		   => $upOrDown,
				'user_id'	   => Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->user_id : null,
				'ip'		   => $request->getClientIp(),
			));
			if (Core_Services_Config::get('vote', 'auth_required', 'false') == 'true' && Zend_Auth::getInstance()->hasIdentity() == false) {
				$result = false;
			} else if (Vote_Services_Vote::count($vote->getProperties()) == 0) {
				$result = true;
				Vote_Services_Vote::add($vote);
			}
		}
		
		return array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'vote'	 => $upOrDown,
		);
	}
}
