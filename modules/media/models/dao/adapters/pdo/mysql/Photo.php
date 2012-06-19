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

class Media_Models_Dao_Adapters_Pdo_Mysql_Photo extends Core_Base_Models_Dao
	implements Media_Models_Dao_Interface_Photo
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Media_Models_Photo($entity);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::add()
	 */
	public function add($photo)
	{
		$this->_conn->insert($this->_prefix . 'media_photo', 
							array(
								'flickr_id'		  => $photo->flickr_id,
								'slug'			  => $photo->slug,
								'title'			  => $photo->title,
								'description'	  => $photo->description,
								'image_square'	  => $photo->image_square,
								'image_thumbnail' => $photo->image_thumbnail,
								'image_small'	  => $photo->image_small,
								'image_crop'	  => $photo->image_crop,
								'image_medium'	  => $photo->image_medium,
								'image_large'	  => $photo->image_large,
								'image_original'  => $photo->image_original,
								'uploaded_date'	  => $photo->uploaded_date,
								'user_id'		  => $photo->user_id,
								'user_name'	   	  => $photo->user_name,
								'photographer'	  => $photo->photographer,
								'num_views'		  => $photo->num_views,
								'num_downloads'	  => $photo->num_downloads,
								'status'		  => $photo->status,
								'language'	      => $photo->language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'media_photo');
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::addToAlbum()
	 */
	public function addToAlbum($photoId, $albumId, $index = 0)
	{
		$this->_conn->delete($this->_prefix . 'media_photo_album_assoc',
							array(
								'photo_id = ?' => $photoId,
								'album_id = ?' => $albumId,
							));
		$this->_conn->insert($this->_prefix . 'media_photo_album_assoc',
							array(
								'photo_id' => $photoId,
								'album_id' => $albumId,
								'ordering' => $index,
							));
		
		// Update the number of photos for album
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'num_photos' => new Zend_Db_Expr('num_photos + 1'),
							),
							array(
								'album_id = ?' => $albumId,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('p' => $this->_prefix . 'media_photo'), array('num_photos' => 'COUNT(*)'));
		if (isset($criteria['album_id']) && !empty($criteria['album_id'])) {
			$select->joinLeft(array('pa' => $this->_prefix . 'media_photo_album_assoc'), 'p.photo_id = pa.photo_id', array())
				   ->where('pa.album_id = ?', $criteria['album_id']);
		}
		if (isset($criteria['tag']) && ($criteria['tag'] instanceof Tag_Models_Tag)) {
			$select->joinLeft(array('te' => $this->_prefix . 'tag_entity_assoc'), 'p.photo_id = te.entity_id', array())
				   ->where('te.entity_class = ?', 'Media_Models_Photo')
				   ->where('te.tag_id = ?', $criteria['tag']->tag_id);
		}
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('p.' . $key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("p.title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		return $select->limit(1)->query()->fetch()->num_photos;
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::delete()
	 */
	public function delete($photo)
	{
		$albumIds = "SELECT album_id FROM " . $this->_prefix . "media_photo_album_assoc
					 WHERE photo_id = " . $this->_conn->quote($photo->photo_id);
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'num_photos' => new Zend_Db_Expr('num_photos - 1'),
							),
							array(
								'album_id IN (?)' => new Zend_Db_Expr($albumIds),
							));
		$this->_conn->delete($this->_prefix . 'media_photo_album_assoc',
							array(
								'photo_id = ?' => $photo->photo_id,
							));
		$this->_conn->delete($this->_prefix . 'media_photo',
							array(
								'photo_id = ?' => $photo->photo_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('p' => $this->_prefix . 'media_photo'));
		if (isset($criteria['photo_ids']) && !empty($criteria['photo_ids'])) {
			$ids = is_array($criteria['photo_ids']) ? implode(',', $criteria['photo_ids']) : $criteria['photo_ids']; 
			$select->where('p.photo_id IN (?)', new Zend_Db_Expr($ids));
		}
		if (isset($criteria['album_id']) && !empty($criteria['album_id'])) {
			$select->joinLeft(array('pa' => $this->_prefix . 'media_photo_album_assoc'), 'p.photo_id = pa.photo_id', array('ordering'))
				   ->where('pa.album_id = ?', $criteria['album_id'])
				   ->order('ordering DESC');
		}
		if (isset($criteria['tag']) && ($criteria['tag'] instanceof Tag_Models_Tag)) {
			$select->joinLeft(array('te' => $this->_prefix . 'tag_entity_assoc'), 'p.photo_id = te.entity_id', array())
				   ->where('te.entity_class = ?', 'Media_Models_Photo')
				   ->where('te.tag_id = ?', $criteria['tag']->tag_id);
		}
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('p.' . $key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("p.title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'p.photo_id';
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
	 * @see Media_Models_Dao_Interface_Photo::getByFlickrId()
	 */
	public function getByFlickrId($flickrPhotoId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'media_photo')
					->where('flickr_id = ?', $flickrPhotoId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Media_Models_Photo($row);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::getById()
	 */
	public function getById($photoId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'media_photo')
					->where('photo_id = ?', $photoId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Media_Models_Photo($row);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::increaseNumDownloads()
	 */
	public function increaseNumDownloads($photo)
	{
		$this->_conn->update($this->_prefix . 'media_photo',
							array(
								'num_downloads' => new Zend_Db_Expr('num_downloads + 1'),
							),
							array(
								'photo_id = ?' => $photo->photo_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::increaseNumViews()
	 */
	public function increaseNumViews($photo)
	{
		$this->_conn->update($this->_prefix . 'media_photo',
							array(
								'num_views' => new Zend_Db_Expr('num_views + 1'),
							),
							array(
								'photo_id = ?' => $photo->photo_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::removeFromAlbum()
	 */
	public function removeFromAlbum($photoId, $albumId)
	{
		$this->_conn->delete($this->_prefix . 'media_photo_album_assoc',
							array(
								'photo_id = ?' => $photoId,
								'album_id = ?' => $albumId,
							));
		$this->_conn->update($this->_prefix . 'media_album',
							array(
								'num_photos' => new Zend_Db_Expr('num_photos - 1'),
							),
							array(
								'album_id = ?' => $albumId,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::rename()
	 */
	public function rename($photo)
	{
		return $this->_conn->update($this->_prefix . 'media_photo',
									array(
										'title' => $photo->title,
									),
									array(
										'photo_id = ?' => $photo->photo_id,
									));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::update()
	 */
	public function update($photo)
	{
		return $this->_conn->update($this->_prefix . 'media_photo',
									array(
										'title'			  => $photo->title,
										'description'	  => $photo->description,
										'photographer'	  => $photo->photographer,
										'image_square'	  => $photo->image_square,
										'image_thumbnail' => $photo->image_thumbnail,
										'image_small'	  => $photo->image_small,
										'image_crop'	  => $photo->image_crop,
										'image_medium'	  => $photo->image_medium,
										'image_large'	  => $photo->image_large,
										'image_original'  => $photo->image_original,
									),
									array(
										'photo_id = ?' => $photo->photo_id,
									));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::updateStatus()
	 */
	public function updateStatus($photo)
	{
		$this->_conn->update($this->_prefix . 'media_photo',
							array(
								'status'		 => $photo->status,
								'activated_date' => $photo->activated_date,
							),
							array(
								'photo_id = ?' => $photo->photo_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::updateUsername()
	 */
	public function updateUsername($user)
	{
		$this->_conn->update($this->_prefix . 'media_photo',
							array(
								'user_name' => $user->user_name,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::deleteTag()
	 */
	public function deleteTag($tag)
	{
		// Do nothing
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::getTags()
	 */
	public function getTags($photo)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('t' => $this->_prefix . 'tag'))
					   ->joinInner(array('te' => $this->_prefix . 'tag_entity_assoc'), 't.tag_id = te.tag_id', array())
					   ->where('te.entity_id = ?', $photo->photo_id)
					   ->where('te.entity_class = ?', get_class($photo))
					   ->query()
					   ->fetchAll();
		$tags = array();
		foreach ($result as $row) {
			$tags[] = new Tag_Models_Tag($row);
		}
		return $tags;
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::setTags()
	 */
	public function setTags($photo, $tags)
	{
		$class = get_class($photo);
		$this->_conn->delete($this->_prefix . 'tag_entity_assoc',
							array(
								'entity_id = ?'	   => $photo->photo_id,
								'entity_class = ?' => $class,
							));
		if ($tags) {
			foreach ($tags as $tag) {
				$this->_conn->insert($this->_prefix . 'tag_entity_assoc',
									array(
										'tag_id'	   => $tag->tag_id,
										'entity_id'	   => $photo->photo_id,
										'entity_class' => $class,
									));
			}
		}
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::increaseNumComments()
	 */
	public function increaseNumComments($comment, $inc)
	{
		$numComments = 'num_comments' . $inc;
		$this->_conn->update($this->_prefix . 'media_photo',
							array(
								'num_comments' => new Zend_Db_Expr($numComments),
							),
							array(
								'photo_id = ?' => $comment->entity_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Photo::increaseNumVotes()
	 */
	public function increaseNumVotes($vote)
	{
		$data = $vote->vote == 1
			  ? array('num_ups' => new Zend_Db_Expr('num_ups + 1'))
			  : array('num_downs' => new Zend_Db_Expr('num_downs + 1'));
		$this->_conn->update($this->_prefix . 'media_photo',
							$data,
							array(
								'photo_id = ?' => $vote->entity_id,
							));
	}
}
