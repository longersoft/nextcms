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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Vote_Models_Dao_Adapters_Pdo_Mysql_Vote extends Core_Base_Models_Dao
	implements Vote_Models_Dao_Interface_Vote
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Vote_Models_Vote($entity);
	}
	
	/**
	 * @see Vote_Models_Dao_Interface_Vote::add()
	 */
	public function add($vote)
	{
		$this->_conn->insert($this->_prefix . 'vote',
							array(
								'entity_id'    => $vote->entity_id,
								'entity_class' => $vote->entity_class,
								'vote'		   => $vote->vote,
								'user_id'	   => $vote->user_id,
								'ip'		   => $vote->ip,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'vote');
	}
	
	/**
	 * @see Vote_Models_Dao_Interface_Vote::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'vote', array('num_votes' => 'COUNT(*)'));
		foreach (array('entity_id', 'entity_class', 'user_id', 'ip') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		return $select->limit(1)->query()->fetch()->num_votes;
	}
	
	/**
	 * @see Vote_Models_Dao_Interface_Vote::getNumVotes()
	 */
	public function getNumVotes($entity)
	{
		$numUps = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'vote', array('num_ups' => 'COUNT(*)'))
					   ->where('entity_id = ?', $entity['entity_id'])
					   ->where('entity_class = ?', $entity['entity_class'])
					   ->where('vote = ?', 1)
					   ->limit(1)
					   ->query()
					   ->fetch()
					   ->num_ups;
		$numDowns = $this->_conn
						 ->select()
						 ->from($this->_prefix . 'vote', array('num_downs' => 'COUNT(*)'))
						 ->where('entity_id = ?', $entity['entity_id'])
						 ->where('entity_class = ?', $entity['entity_class'])
						 ->where('vote = ?', -1)
						 ->limit(1)
						 ->query()
						 ->fetch()
						 ->num_downs;
		return array(
			'num_ups'	=> $numUps,
			'num_downs' => $numDowns,
		);
	}
}
