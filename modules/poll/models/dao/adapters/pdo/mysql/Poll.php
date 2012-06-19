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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_Models_Dao_Adapters_Pdo_Mysql_Poll extends Core_Base_Models_Dao
	implements Poll_Models_Dao_Interface_Poll
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Poll_Models_Poll($entity);
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::add()
	 */
	public function add($poll)
	{
		// Add new poll
		$this->_conn->insert($this->_prefix . 'poll',
							array(
								'title'			   => $poll->title,
								'description'	   => $poll->description,
								'created_user'	   => $poll->created_user,
								'created_date'	   => $poll->created_date,
								'multiple_options' => $poll->multiple_options,
								'language'		   => $poll->language,
								'translations'	   => $poll->translations,
							));
		$pollId = $this->_conn->lastInsertId($this->_prefix . 'poll');
		
		// Add poll options
		if ($poll->options && is_array($poll->options) && count($poll->options) > 0) {
			$index = 0;
			foreach ($poll->options as $option) {
				if ($option) {
					$this->_conn->insert($this->_prefix . 'poll_option',
										array(
											'poll_id'	  => $pollId,
											'ordering'	  => $index,
											'title'		  => $option,
											'num_choices' => 0,
										));
					$index++;
				}
			}
		}
		
		if (!$poll->translations) {
			$this->_conn->update($this->_prefix . 'poll', 
								array(
									'translations' => Zend_Json::encode(array($poll->language => (string) $pollId)),
								),
								array(
									'poll_id = ?' => $pollId,
								));
		} else {
			$translations = Zend_Json::decode($poll->translations);
			$translations[$poll->language] = (string) $pollId;
			
			$this->_conn->update($this->_prefix . 'poll', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $poll->translations,
								));
		}
		
		return $pollId;
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'poll', array('num_polls' => 'COUNT(*)'));
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_polls;
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::delete()
	 */
	public function delete($poll)
	{
		$this->_conn->delete($this->_prefix . 'poll_option',
							array(
								'poll_id = ?' => $poll->poll_id,
							));
		$this->_conn->delete($this->_prefix . 'poll',
							array(
								'poll_id = ?' => $poll->poll_id,
							));
							
		if ($poll->translations) {
			$translations = Zend_Json::decode($poll->translations);
			unset($translations[$poll->language]);
			
			$this->_conn->update($this->_prefix . 'poll', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $poll->translations,
								));
		}
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'poll');
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'poll_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::getById()
	 */
	public function getById($pollId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'poll')
					->where('poll_id = ?', $pollId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Poll_Models_Poll($row);
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::increaseNumChoices()
	 */
	public function increaseNumChoices($poll, $answers)
	{
		if (is_array($answers) && count($answers) > 0) {
			$this->_conn->update($this->_prefix . 'poll_option', 
								array(
									'num_choices' => new Zend_Db_Expr('num_choices + 1'),
								),
								array(
									'poll_id = ?'		=> $poll->poll_id,
									'option_id IN (?)' => new Zend_Db_Expr(implode(',', $answers)),
								));
		}
	}
	
	/**
	 * @see Poll_Models_Dao_Interface_Poll::update()
	 */
	public function update($poll)
	{
		// Update poll information
		$this->_conn->update($this->_prefix . 'poll', 
							array(
								'title'			   => $poll->title,
								'description'	   => $poll->description,
								'multiple_options' => $poll->multiple_options,
							),
							array(
								'poll_id = ?' => $poll->poll_id,
							));
		// Delete all poll options
		$this->_conn->delete($this->_prefix . 'poll_option',
							array(
								'poll_id = ?' => $poll->poll_id,
							));
							
		// Add the new options
		if ($poll->options && is_array($poll->options) && count($poll->options) > 0) {
			foreach ($poll->options as $option) {
				if ($option->title) {
					$this->_conn->insert($this->_prefix . 'poll_option',
										array(
											'poll_id'	  => $option->poll_id,
											'ordering'	  => $option->ordering,
											'title'		  => $option->title,
											'num_choices' => $option->num_choices,
										));
				}
			}
		}
		
		// Update translations
		if ($poll->new_translations && $poll->new_translations != $poll->translations) {
			// Update old translated polls
			$translations = Zend_Json::decode($poll->translations);
			unset($translations[$poll->language]);
			$this->_conn->update($this->_prefix . 'poll', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $poll->translations,
								));
			
			// Update the poll
			$translations = Zend_Json::decode($poll->new_translations);
			$translations[$poll->language] = (string) $poll->poll_id;
			
			$where[] = 'poll_id = ' . $this->_conn->quote($poll->poll_id) . ' OR translations = ' . $this->_conn->quote($poll->new_translations);
			$this->_conn->update($this->_prefix . 'poll', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								$where);
		}
	}
}
