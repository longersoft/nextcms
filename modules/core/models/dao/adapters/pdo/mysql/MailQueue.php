<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_MailQueue extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_MailQueue
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_MailQueue($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_MailQueue::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_mail_queue');
		if (isset($criteria['success']) && !empty($criteria['success'])) {
			$select->where('success = ?', $criteria['success']);
		}
		if (isset($criteria['max_attempts']) && !empty($criteria['max_attempts'])) {
			$select->where('num_attempts < ?', $criteria['max_attempts']);
		}
		
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'mail_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'ASC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_MailQueue::increaseAttempts()
	 */
	public function increaseAttempts($mailQueue)
	{
		$this->_conn->update($this->_prefix . 'core_mail_queue',
							array(
								'num_attempts' => new Zend_Db_Expr('num_attempts + 1'),
							),
							array(
								'mail_id = ?' => $mailQueue->mail_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_MailQueue::queue()
	 */
	public function queue($mailQueue)
	{
		$this->_conn->insert($this->_prefix . 'core_mail_queue',
							array(
								'from_name'	   => $mailQueue->from_name,
								'from_email'   => $mailQueue->from_email,
								'to_name'	   => $mailQueue->to_name,
								'to_email'     => $mailQueue->to_email,
								'num_attempts' => $mailQueue->num_attempts,
								'subject'	   => $mailQueue->subject,
								'content'	   => $mailQueue->content,
								'queued_date'  => $mailQueue->queued_date,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_mail_queue');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_MailQueue::dequeue()
	 */
	public function dequeue($mailQueue)
	{
		$this->_conn->insert($this->_prefix . 'core_mail_sent',
							array(
								'from_name'	   => $mailQueue->from_name,
								'from_email'   => $mailQueue->from_email,
								'to_name'	   => $mailQueue->to_name,
								'to_email'     => $mailQueue->to_email,
								'subject'	   => $mailQueue->subject,
								'content'	   => $mailQueue->content,
								'queued_date'  => $mailQueue->queued_date,
								'num_attempts' => $mailQueue->num_attempts,
								'success'	   => $mailQueue->success,
								'sent_date'	   => $mailQueue->sent_date,
							));
		$this->_conn->delete($this->_prefix . 'core_mail_queue',
							array(
								'mail_id = ?' => $mailQueue->mail_id,
							));
	}
}
