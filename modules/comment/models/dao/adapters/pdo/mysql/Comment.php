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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_Models_Dao_Adapters_Pdo_Mysql_Comment extends Core_Base_Models_Dao
	implements Comment_Models_Dao_Interface_Comment
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Comment_Models_Comment($entity);
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::add()
	 */
	public function add($comment)
	{
		$this->_conn->insert($this->_prefix . 'comment', 
							array(
								'entity_id'		 => $comment->entity_id,
								'entity_class'	 => $comment->entity_class,
								'entity_module'  => $comment->entity_module,
								'title'			 => $comment->title,
								'content'		 => $comment->content,
								'full_name'		 => $comment->full_name,
								'web_site'		 => $comment->web_site,
								'email'			 => $comment->email,
								'ip'			 => $comment->ip,
								'user_agent'	 => $comment->user_agent,
								'created_user'	 => $comment->created_user,
								'created_date'	 => $comment->created_date,
								'reply_to'		 => $comment->reply_to,
								'language'		 => $comment->language,
							));
		$commentId = $this->_conn->lastInsertId($this->_prefix . 'comment');
		
		// Update the order of comments in the thread
		$maxOrdering = $this->_conn
							->select()
							->from($this->_prefix . 'comment', array('max_ordering' => 'MAX(ordering)'))
							->where('entity_id = ?', $comment->entity_id)
							->where('entity_class = ?', $comment->entity_class)
							->where('entity_module = ?', $comment->entity_module)
							->query()
							->fetch()
							->max_ordering;
		$depth = 0;
		$path  = $commentId . '_';
		if ($comment->reply_to) {
			$reply = $this->getById($comment->reply_to); 
			if ($reply) {
				$row 		 = $this->_conn
									->select()
									->from($this->_prefix . 'comment', array('max_ordering' => 'MAX(ordering)'))
									->where('entity_id = ?', $comment->entity_id)
									->where('entity_class = ?', $comment->entity_class)
									->where('entity_module = ?', $comment->entity_module)
									->where('path LIKE ?', $reply->path . '%')
									->query()
									->fetch();
				$maxOrdering = (null == $row) ? $reply->ordering : $row->max_ordering;
				$path		 = $reply->path . $path;
				$depth		 = $reply->depth + 1;
				
				$this->_conn->update($this->_prefix . 'comment',
									array(
										'ordering' => new Zend_Db_Expr('ordering + 1'),
									),
									array(
										'entity_id = ?'		=> $comment->entity_id,
										'entity_class = ?'  => $comment->entity_class,
										'entity_module = ?' => $comment->entity_module,
										'ordering > ?'		=> $maxOrdering,
									));
			}
		}
		
		$this->_conn->update($this->_prefix . 'comment', 
							array(
								'ordering' => $maxOrdering + 1,
								'depth'	   => $depth,
								'path'	   => $path,
							), array(
								'comment_id = ?' => $commentId,
							));
		return $commentId;
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'comment', array('num_comments' => 'COUNT(*)'));

		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		foreach (array('entity_id', 'entity_class', 'entity_module') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where("$key = ?", $criteria[$key]);
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('status = ?', $criteria['status']);
		}
		return $select->limit(1)->query()->fetch()->num_comments;
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::countThreads()
	 */
	public function countThreads($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'comment', array('num_threads' => 'COUNT(DISTINCT CONCAT(entity_id, "_", entity_class, "_", entity_module))'));
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('status = ?', $criteria['status']);
		}
		return $select->limit(1)->query()->fetch()->num_threads;
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::delete()
	 */
	public function delete($comment)
	{
		$this->_conn->delete($this->_prefix . 'comment',
							array(
								'comment_id = ?' => $comment->comment_id,
							));
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'comment');
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		foreach (array('entity_id', 'entity_class', 'entity_module') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where("$key = ?", $criteria[$key]);
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('status = ?', $criteria['status']);
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'comment_id';
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
	 * @see Comment_Models_Dao_Interface_Comment::findThreads()
	 */
	public function findThreads($criteria = array(), $offset = null, $count = null)
	{
		$query = (isset($criteria['status']) && !empty($criteria['status']))
				? "c.comment_id IN (SELECT MAX(c2.comment_id) FROM " . $this->_prefix . "comment AS c2 WHERE c2.status = " . $this->_conn->quote($criteria['status']) . " GROUP BY CONCAT(c2.entity_id, '_', c2.entity_class, '_', c2.entity_module))"
				: "c.comment_id IN (SELECT MAX(c2.comment_id) FROM " . $this->_prefix . "comment AS c2 GROUP BY CONCAT(c2.entity_id, '_', c2.entity_class, '_', c2.entity_module))";
		
		$select = $this->_conn
					   ->select()
					   ->from(array('c' => $this->_prefix . 'comment'))
					   ->where($query);
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'comment_id';
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
	 * @see Comment_Models_Dao_Interface_Comment::getById()
	 */
	public function getById($commentId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'comment')
					->where('comment_id = ?', $commentId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Comment_Models_Comment($row);
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::update()
	 */
	public function update($comment)
	{
		$this->_conn->update($this->_prefix . 'comment', 
							array(
								'title'		=> $comment->title,
								'content'	=> $comment->content,
								'full_name' => $comment->full_name,
								'web_site'	=> $comment->web_site,
								'email'		=> $comment->email,
							), 
							array(
								'comment_id = ?' => $comment->comment_id,
							));
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::updateStatus()
	 */
	public function updateStatus($comment)
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$data = ($comment->status == Comment_Models_Comment::STATUS_ACTIVATED || $comment->status == Comment_Models_Comment::STATUS_NOT_ACTIVATED)
				? array(
					'status' 		 => $comment->status,
					'activated_user' => $user->user_id,
					'activated_date' => date('Y-m-d H:i:s'),
				)
				: array(
					'status' => $comment->status,
				);
		
		$this->_conn->update($this->_prefix . 'comment', 
							$data,
							array(
								'comment_id = ?' => $comment->comment_id,
							));
	}
	
	/**
	 * @see Comment_Models_Dao_Interface_Comment::increaseNumVotes()
	 */
	public function increaseNumVotes($vote)
	{
		$data = $vote->vote == 1
			  ? array('num_ups' => new Zend_Db_Expr('num_ups + 1'))
			  : array('num_downs' => new Zend_Db_Expr('num_downs + 1'));
		$this->_conn->update($this->_prefix . 'comment',
							$data,
							array(
								'comment_id = ?' => $vote->entity_id,
							));
	}	
}
