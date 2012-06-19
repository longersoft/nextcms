<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	models
 * @since		1.0
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Models_Dao_Adapters_Pdo_Mysql_Attachment extends Core_Base_Models_Dao
	implements File_Models_Dao_Interface_Attachment
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new File_Models_Attachment($entity);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Attachment::add()
	 */
	public function add($attachment)
	{
		$this->_conn->insert($this->_prefix . 'file_attachment',
							array(
								'hash'			=> $attachment->hash,
								'title'			=> $attachment->title,
								'slug'			=> $attachment->slug,
								'description'	=> $attachment->description,
								'name'			=> $attachment->name,
								'extension'		=> $attachment->extension,
								'path'			=> $attachment->path,
								'size'			=> $attachment->size,
								'uploaded_user' => $attachment->uploaded_user,
								'uploaded_date' => $attachment->uploaded_date,
								'num_downloads' => 0,
								'auth_required' => $attachment->auth_required,
								'password'		=> $attachment->password,
								'language'		=> $attachment->language,
								'translations'	=> $attachment->translations,
							));
		$attachmentId = $this->_conn->lastInsertId($this->_prefix . 'file_attachment');
		
		if (!$attachment->translations) {
			$this->_conn->update($this->_prefix . 'file_attachment', 
								array(
									'translations' => Zend_Json::encode(array($attachment->language => (string) $attachmentId)),
								),
								array(
									'attachment_id = ?' => $attachmentId,
								));
		} else {
			$translations = Zend_Json::decode($attachment->translations);
			$translations[$attachment->language] = (string) $attachmentId;
			
			$this->_conn->update($this->_prefix . 'file_attachment', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $attachment->translations,
								));
		}
		
		return $attachmentId;
	}
	
	/**
	 * @see File_Models_Dao_Interface_Attachment::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'file_attachment', array('num_attachments' => 'COUNT(*)'));
		foreach (array('hash', 'language', 'slug') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%' OR name LIKE '%" . $keyword . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_attachments;
	}
	
	/**
	 * @see File_Models_Dao_Interface_Attachment::delete()
	 */
	public function delete($attachment)
	{
		$this->_conn->delete($this->_prefix . 'file_attachment',
							array(
								'attachment_id = ?' => $attachment->attachment_id,
							));
		
		if ($attachment->translations) {
			$translations = Zend_Json::decode($attachment->translations);
			unset($translations[$attachment->language]);
			
			$this->_conn->update($this->_prefix . 'file_attachment', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $attachment->translations,
								));
		}
	}
	
	/**
	 * @see File_Models_Dao_Interface_Attachment::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'file_attachment');
		if (isset($criteria['attachment_ids']) && !empty($criteria['attachment_ids'])) {
			$ids = is_array($criteria['attachment_ids']) ? implode(',', $criteria['attachment_ids']) : $criteria['attachment_ids']; 
			$select->where('attachment_id IN (?)', new Zend_Db_Expr($ids));
		}
		foreach (array('hash', 'language', 'slug') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%' OR name LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'attachment_id';
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
	 * @see File_Models_Dao_Interface_Attachment::getById()
	 */
	public function getById($attachmentId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'file_attachment')
					->where('attachment_id = ?', $attachmentId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new File_Models_Attachment($row);
	}
	
	/**
	 * @see File_Models_Dao_Interface_Attachment::increaseNumDownloads()
	 */
	public function increaseNumDownloads($attachment)
	{
		$this->_conn->update($this->_prefix . 'file_attachment',
							array(
								'num_downloads' => new Zend_Db_Expr('num_downloads + 1'),
							),
							array(
								'attachment_id = ?' => $attachment->attachment_id,
							));
	}
	
	/**
	 * @see File_Models_Dao_Interface_Attachment::update()
	 */
	public function update($attachment)
	{
		$data = array(
			'title'			=> $attachment->title,
			'slug'			=> $attachment->slug,
			'description'	=> $attachment->description,
			'name'			=> $attachment->name,
			'extension'		=> $attachment->extension,
			'path'			=> $attachment->path,
			'size'			=> $attachment->size,
			'auth_required' => $attachment->auth_required,
			'password'		=> $attachment->password,
		);
		
		$this->_conn->update($this->_prefix . 'file_attachment', 
							$data,
							array(
								'attachment_id = ?' => $attachment->attachment_id,
							));
									
		// Update translations
		if ($attachment->new_translations && $attachment->new_translations != $attachment->translations) {
			$translations = Zend_Json::decode($attachment->translations);
			unset($translations[$attachment->language]);
			$this->_conn->update($this->_prefix . 'file_attachment', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $attachment->translations,
								));
			
			$translations = Zend_Json::decode($attachment->new_translations);
			$translations[$attachment->language] = (string) $attachment->attachment_id;
			
			$where[] = 'attachment_id = ' . $this->_conn->quote($attachment->attachment_id) . ' OR translations = ' . $this->_conn->quote($attachment->new_translations);
			$this->_conn->update($this->_prefix . 'file_attachment', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								$where);
		}
	}
	
	public function updateLastDownload($attachment)
	{
		$this->_conn->update($this->_prefix . 'file_attachment', 
							array(
								'last_download' => $attachment->last_download,
							),
							array(
								'attachment_id = ?' => $attachment->attachment_id,
							));
	}
}
