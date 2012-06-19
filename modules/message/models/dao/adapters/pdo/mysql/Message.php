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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Models_Dao_Adapters_Pdo_Mysql_Message extends Core_Base_Models_Dao
	implements Message_Models_Dao_Interface_Message
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Message_Models_Message($entity);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::add()
	 */
	public function add($message)
	{
		$toAddress = ($message->to_address && is_array($message->to_address)) ? $message->to_address : null;
		$this->_conn->insert($this->_prefix . 'message',
							array(
								'root_id'	  => $message->root_id,
								'sent_user'	  => $message->sent_user,
								'subject'	  => $message->subject,
								'content'	  => $message->content,
								'sent_date'	  => $message->sent_date,
								'reply_to'	  => $message->reply_to,
								'to_address'  => $toAddress ? implode(',', $toAddress) : null,
								'attachments' => $message->attachments ? Zend_Json::encode($message->attachments) : null,
							));
		$messageId = $this->_conn->lastInsertId($this->_prefix . 'message');
		
		if ($message->root_id == 0) {
			// In case of sending new message
			$message->root_id = $messageId;
			$this->_conn->update($this->_prefix . 'message',
								array(
									'root_id' => $messageId,
								),
								array(
									'message_id = ?' => $messageId,
								));
		}
		
		// Add to the recipient table
		if ($toAddress) {
			$this->_conn->insert($this->_prefix . 'message_recipient',
								array(
									'message_id' => $messageId,
									'root_id'	 => $message->root_id,
									'from_user'	 => $message->sent_user,
									'to_user'	 => $message->sent_user,
									'deleted'	 => 0,
									'unread'	 => 0,
									'starred' 	 => 0,
									'folder_id'  => Message_Models_Folder::FOLDER_SENT,
								));
			foreach ($toAddress as $to) {
				// Run the filter
				$filters = $this->_conn
					   			->select()
					   			->from($this->_prefix . 'message_filter')
					   			->where('user_id = ?', $to)
					   			->query()
					   			->fetchAll();
				$filter = null;
				if ($filters && count($filters) > 0) {
					foreach ($filters as $row) {
						$filter = new Message_Models_Filter($row);
						if ($filter->match($message)) {
							break;
						} else {
							$filter = null;
						}
					}
				}
				$actions = array(
					'deleted'   => 0,
					'unread'    => 1,
					'starred'   => 0,
					'folder_id' => Message_Models_Folder::FOLDER_INBOX,
				);
				if ($filter) {
					$filterActions = Zend_Json::decode($filter->actions);
					$actions = array(
						'deleted'   => $filterActions[Message_Models_Filter::ACTION_DELETE],
						'unread'    => 1 - $filterActions[Message_Models_Filter::ACTION_MARK_READ],
						'starred'   => $filterActions[Message_Models_Filter::ACTION_START],
						'folder_id' => $filter->folder_id ? $filter->folder_id : Message_Models_Folder::FOLDER_INBOX,
					);
				}
				
				$this->_conn->insert($this->_prefix . 'message_recipient', 
									array_merge(array(
										'message_id' => $messageId,
										'root_id'	 => $message->root_id,
										'from_user'	 => $message->sent_user,
										'to_user'	 => $to,
									), $actions));
			}
		}
		
		// Add attachments
		if ($message->attachments) {
			foreach ($message->attachments as $file) {
				$file = Zend_Json::decode($file);
				$this->_conn->insert($this->_prefix . 'message_attachment', 
									array(
										'message_id' => $messageId,
										'path'		 => $file['path'],
										'name'		 => $file['name'],
										'extension'	 => $file['extension'],
										'size'		 => $file['size'],
									));
			}
		}
		
		return $messageId;
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::canDownloadAttachment()
	 */
	public function canDownloadAttachment($path, $user)
	{
		$userId		  = $user->user_id;
		$attachmentId = $this->_conn
							 ->select()
							 ->distinct()
							 ->from(array('a' => $this->_prefix . 'message_attachment'), array('attachment_id'))
							 ->joinInner(array('r' => $this->_prefix . 'message_recipient'), 'r.message_id = a.message_id', array())
							 ->where('a.path = ?', $path)
							 ->where('r.from_user = ' . $userId . ' OR r.to_user = ' . $userId)
							 ->query()
							 ->fetch()
							 ->attachment_id;
		if ($attachmentId == null || $attachmentId == '') {
			return false;
		}
		
		// Update the last download
		$this->_conn->update($this->_prefix . 'message_attachment',
							array(
								'last_download_date' => date('Y-m-d H:i:s'),
								'last_download_user' => $userId,
								'num_downloads'		 => new Zend_Db_Expr('num_downloads + 1'),
							),
							array(
								'attachment_id = ?' => $attachmentId,
							));
		return true;
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::count()
	 */
	public function count($criteria = array())
	{
		$userId = $this->_conn->quote($criteria['user_id']);
		$select = $this->_conn
					   ->select()
					   ->from(array('m' => $this->_prefix . 'message'), array('message_id'))
					   ->joinInner(array('r' => $this->_prefix . 'message_recipient'), 'm.message_id = r.message_id', array())
					   ->where('r.root_id = ?', $criteria['root_id'])
					   ->where('r.from_user = ' . $userId . ' OR r.to_user = ' . $userId);
		
		if (isset($criteria['deleted']) && $criteria['deleted'] != null) {
			$select->where('r.deleted = ?', $criteria['deleted']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(m.subject LIKE '%" . $keyword . "%' OR m.content LIKE '%" . $keyword . "%')");
		}
		$select->group('m.message_id');
		$result = $select->query()->fetchAll();
		return ($result == null) ? 0 : count($result);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::countThreads()
	 */
	public function countThreads($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('m' => $this->_prefix . 'message'), array('num_threads' => 'COUNT(DISTINCT m.root_id)'))
					   ->joinInner(array('r' => $this->_prefix . 'message_recipient'), 'm.message_id = r.message_id', array())
					   ->where('r.to_user = ?', $criteria['user_id']);
		if (isset($criteria['folder_id']) && $criteria['folder_id'] != null) {
			switch (true) {
				case ($criteria['folder_id'] == Message_Models_Folder::FOLDER_INBOX):
					$select->where('(r.folder_id = ' . $this->_conn->quote(Message_Models_Folder::FOLDER_INBOX) . ' OR r.folder_id = ' . $this->_conn->quote(Message_Models_Folder::FOLDER_SENT) . ')');
					break;
				case ($criteria['folder_id'] != null):
					$select->where('r.folder_id = ?', $criteria['folder_id']);
					break;
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(m.subject LIKE '%" . $keyword . "%' OR m.content LIKE '%" . $keyword . "%')");
		}
		if (isset($criteria['unread']) && $criteria['unread'] != null) {
			$select->where('r.unread = ?', $criteria['unread']);
		}
		if (isset($criteria['starred']) && $criteria['starred'] === true) {
			$select->where('r.starred = 1');
		}
		if (isset($criteria['deleted']) && $criteria['deleted'] != null) {
			$select->where('r.deleted = ?', $criteria['deleted']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(m.subject LIKE '%" . $keyword . "%')");
		}
		
		return $select->limit(1)->query()->fetch()->num_threads;
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::delete()
	 */
	public function delete($message, $user)
	{
		// Check if the message is already deleted or not
		$deleted = $this->_conn
						->select()
						->from($this->_prefix . 'message_recipient', array('deleted'))
						->where('message_id = ?', $message->message_id)
						->where('from_user = ?', $message->sent_user)
						->where('to_user = ?', $user->user_id)
						->limit(1)
						->query()
						->fetch()
						->deleted;
		$deleted = (string) $deleted;
		if ($deleted == '1') {
			// Delete message forever
			$this->_conn->delete($this->_prefix . 'message_recipient',
								array(
									'message_id = ?' => $message->message_id,
									'from_user = ?'	 => $message->sent_user,
									'to_user = ?'	 => $user->user_id,
								));
			// Check if other users already deleted the message
			$total = $this->_conn
						  ->select()
						  ->from($this->_prefix . 'message_recipient', array('total' => 'COUNT(*)'))
						  ->where('message_id = ?', $message->message_id)
						  ->limit(1)
						  ->query()
						  ->fetch()
						  ->total;
			if ($total == 0) {
				// Remove the message
				$this->_conn->delete($this->_prefix . 'message',
									array(
										'message_id = ?' => $message->message_id,
									));
			}
		} else {
			// Update the deleted status
			$this->_conn->update($this->_prefix . 'message_recipient',
								array(
									'deleted' => '1',
								),
								array(
									'message_id = ?' => $message->message_id,
									'from_user = ?'	 => $message->sent_user,
									'to_user = ?'	 => $user->user_id,
								));
		}
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::emptyTrash()
	 */
	public function emptyTrash($user)
	{
		$userId = $this->_conn->quote($user->user_id);
		$this->_conn->delete($this->_prefix . 'message_recipient',
							array(
								'(from_user = ' . $userId . ' OR to_user = ' . $userId . ')',
								'deleted' => '1',
							));
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$userId = $this->_conn->quote($criteria['user_id']);
		$select = $this->_conn
					   ->select()
					   ->from(array('m' => $this->_prefix . 'message'))
					   ->joinInner(array('u' => $this->_prefix . 'core_user'), 'm.sent_user = u.user_id', array('from_user_name' => 'u.user_name', 'signature'))
					   ->joinInner(array('r' => $this->_prefix . 'message_recipient'), 'm.message_id = r.message_id', array('folder_id', 'deleted', 'unread', 'starred'))
					   ->where('r.root_id = ?', $criteria['root_id'])
					   ->where('r.from_user = ' . $userId . ' OR r.to_user = ' . $userId);
		
		if (isset($criteria['deleted']) && $criteria['deleted'] != null) {
			$select->where('r.deleted = ?', $criteria['deleted']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(m.subject LIKE '%" . $keyword . "%' OR m.content LIKE '%" . $keyword . "%')");
		}
		$select->group('m.message_id');
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'm.message_id';
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
	 * @see Message_Models_Dao_Interface_Message::findThreads()
	 */
	public function findThreads($criteria = array(), $offset = null, $count = null)
	{
		// Find the latest message in the thread
		// FIXME: It does not get the unread, starred status correctly
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix. 'message_recipient', array('message_id' => 'MAX(message_id)', 'unread', 'starred', 'num_messages' => 'COUNT(root_id)'))
					   ->where('to_user = ?', $criteria['user_id'])
					   ->group('root_id');
		if (isset($criteria['folder_id']) && $criteria['folder_id'] != null) {
			switch (true) {
				case ($criteria['folder_id'] == Message_Models_Folder::FOLDER_INBOX):
					$select->where('(folder_id = ' . $this->_conn->quote(Message_Models_Folder::FOLDER_INBOX) . ' OR folder_id = ' . $this->_conn->quote(Message_Models_Folder::FOLDER_SENT) . ')');
					break;
				default:
					$select->where('folder_id = ?', $criteria['folder_id']);
					break;
			}
		}
		
		if (isset($criteria['starred']) && $criteria['starred'] === true) {
			$select->where('starred = 1');
		}
		if (isset($criteria['deleted']) && $criteria['deleted'] != null) {
			$select->where('deleted = ?', $criteria['deleted']);
		}
		
		$query  = $select->__toString();
		
		$select = $this->_conn
					   ->select()
					   ->from(array('m' => $this->_prefix . 'message'))
					   ->joinInner(array('u' => $this->_prefix . 'core_user'), 'm.sent_user = u.user_id', array('from_user_name' => 'u.user_name'))
					   ->join(array('m2' => new Zend_Db_Expr('(' . $query . ')')), 'm.message_id = m2.message_id', array('m2.unread', 'm2.starred', 'm2.num_messages'));
		
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(m.subject LIKE '%" . $keyword . "%' OR m.content LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'm.message_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		
		// Sort by the unread status when listing the inbox messages
		if (isset($criteria['folder_id']) && $criteria['folder_id'] == Message_Models_Folder::FOLDER_INBOX) {
			$select->order('m2.unread DESC');
		}
		
		$select->order($criteria['sort_by'] . ' ' . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::getById()
	 */
	public function getById($messageId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'message')
					->where('message_id = ?', $messageId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Message_Models_Message($row);
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::getRecipients()
	 */
	public function getRecipients($userIds)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_user')
					   ->where('user_id IN (' . implode(',', $userIds) . ')');
		$result = $select->query()->fetchAll();
		$users  = array();
		foreach ($result as $row) {
			$users[] = new Core_Models_User($row);
		}
		return $users;
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::move()
	 */
	public function move($message, $user, $folder)
	{
		$this->_conn->update($this->_prefix . 'message_recipient',
							array(
								'folder_id' => $folder->folder_id,
								'deleted'	=> '0',
							),
							array(
								'message_id = ?' => $message->message_id,
								'to_user = ?'	 => $user->user_id,
							));
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::toggleRead()
	 */
	public function toggleRead($message, $user)
	{
		$this->_conn->update($this->_prefix . 'message_recipient',
							array(
								'unread' => new Zend_Db_Expr('1 - unread'),
							),
							array(
								'message_id = ?' => $message->message_id,
								'to_user = ?'	 => $user->user_id,
							));
	}
	
	/**
	 * @see Message_Models_Dao_Interface_Message::toggleStar()
	 */
	public function toggleStar($message, $user)
	{
		$this->_conn->update($this->_prefix . 'message_recipient',
							array(
								'starred' => new Zend_Db_Expr('1 - starred'),
							),
							array(
								'message_id = ?' => $message->message_id,
								'to_user = ?'	 => $user->user_id,
							));
	}
}
