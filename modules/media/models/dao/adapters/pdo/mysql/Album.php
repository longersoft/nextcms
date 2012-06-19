<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	models
 * @since		1.0
 * @version		2012-06-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Models_Dao_Adapters_Pdo_Mysql_Album extends Core_Base_Models_Dao
	implements Media_Models_Dao_Interface_Album
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Media_Models_Album($entity);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::add()
	 */
	public function add($album)
	{
		$this->_conn->insert($this->_prefix . 'media_album', 
							array(
								'title'		   => $album->title,
								'slug'		   => $album->slug,
								'created_date' => $album->created_date,
								'user_id'	   => $album->user_id,
								'user_name'	   => $album->user_name,
								'language'	   => $album->language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'media_album');
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'media_album', array('num_albums' => 'COUNT(*)'));
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		return $select->limit(1)->query()->fetch()->num_albums;
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::delete()
	 */
	public function delete($album)
	{
		$this->_conn->delete($this->_prefix . 'media_photo_album_assoc',
							array(
								'album_id = ?' => $album->album_id,
							));
		return $this->_conn->delete($this->_prefix . 'media_album',
							array(
								'album_id = ?' => $album->album_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'media_album');
		if (isset($criteria['album_ids']) && !empty($criteria['album_ids'])) {
			$ids = is_array($criteria['album_ids']) ? implode(',', $criteria['album_ids']) : $criteria['album_ids']; 
			$select->where('album_id IN (?)', new Zend_Db_Expr($ids));
		}
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'album_id';
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
	 * @see Media_Models_Dao_Interface_Album::getById()
	 */
	public function getById($albumId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'media_album')
					->where('album_id = ?', $albumId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Media_Models_Album($row);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::increaseNumViews()
	 */
	public function increaseNumViews($album)
	{
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'num_views' => new Zend_Db_Expr('num_views + 1'),
							),
							array(
								'album_id = ?' => $album->album_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::rename()
	 */
	public function rename($album)
	{
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'title' => $album->title,
							),
							array(
								'album_id = ?' => $album->album_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::updateCover()
	 */
	public function updateCover($album, $thumbnails, $photo = null)
	{
		$data = array(
			'image_square'	  => $thumbnails['square'],
			'image_thumbnail' => $thumbnails['thumbnail'],
			'image_small'	  => $thumbnails['small'],
			'image_crop'	  => $thumbnails['crop'],
			'image_medium'	  => $thumbnails['medium'],
			'image_large'	  => $thumbnails['large'],
			'image_original'  => $thumbnails['original'],
		);
		if ($photo) {
			$data['cover'] = $photo->photo_id;
		}
		
		return $this->_conn->update($this->_prefix . 'media_album',
									$data,
									array(
										'album_id = ?' => $album->album_id,
									));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::updateStatus()
	 */
	public function updateStatus($album)
	{
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'status'		 => $album->status,
								'activated_date' => $album->activated_date,
							),
							array(
								'album_id = ?' => $album->album_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Album::updateUsername()
	 */
	public function updateUsername($user)
	{
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'user_name' => $user->user_name,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
}
