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

class Media_Models_Dao_Adapters_Pdo_Mysql_Video extends Core_Base_Models_Dao
	implements Media_Models_Dao_Interface_Video
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Media_Models_Video($entity);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::add()
	 */
	public function add($video)
	{
		$this->_conn->insert($this->_prefix . 'media_video', 
							array(
								'slug'			  => $video->slug,
								'title'			  => $video->title,
								'description'	  => $video->description,
								'image_square'	  => $video->image_square,
								'image_thumbnail' => $video->image_thumbnail,
								'image_small'	  => $video->image_small,
								'image_crop'	  => $video->image_crop,
								'image_medium'	  => $video->image_medium,
								'image_large'	  => $video->image_large,
								'image_original'  => $video->image_original,
								'uploaded_date'	  => $video->uploaded_date,
								'user_id'		  => $video->user_id,
								'user_name'		  => $video->user_name,
								'url'			  => $video->url,
								'embed_code'	  => $video->embed_code,
								'duration'		  => $video->duration,
								'credit'		  => $video->credit,
								'num_views'		  => $video->num_views,
								'status'		  => $video->status,
								'language'		  => $video->language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'media_video');
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::addToPlaylist()
	 */
	public function addToPlaylist($videoId, $playlistId, $index = 0)
	{
		$this->_conn->delete($this->_prefix . 'media_video_playlist_assoc',
							array(
								'video_id = ?'	  => $videoId,
								'playlist_id = ?' => $playlistId,
							));
		$this->_conn->insert($this->_prefix . 'media_video_playlist_assoc',
							array(
								'video_id'	  => $videoId,
								'playlist_id' => $playlistId,
								'ordering'	  => $index,
							));
		
		// Update the number of videos for playlist
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'num_videos' => new Zend_Db_Expr('num_videos + 1'),
							),
							array(
								'playlist_id = ?' => $playlistId,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('v' => $this->_prefix . 'media_video'), array('num_videos' => 'COUNT(*)'));
		if (isset($criteria['playlist_id']) && !empty($criteria['playlist_id'])) {
			$select->joinLeft(array('vp' => $this->_prefix . 'media_video_playlist_assoc'), 'v.video_id = vp.video_id', array())
				   ->where('vp.playlist_id = ?', $criteria['playlist_id']);
		}
		if (isset($criteria['tag']) && ($criteria['tag'] instanceof Tag_Models_Tag)) {
			$select->joinLeft(array('te' => $this->_prefix . 'tag_entity_assoc'), 'v.video_id = te.entity_id', array())
				   ->where('te.entity_class = ?', 'Media_Models_Video')
				   ->where('te.tag_id = ?', $criteria['tag']->tag_id);
		}
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('v.' . $key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("v.title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		return $select->limit(1)->query()->fetch()->num_videos;
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::delete()
	 */
	public function delete($video)
	{
		$playlistIds = 'SELECT playlist_id FROM ' . $this->_prefix . 'media_video_playlist_assoc
						WHERE video_id = "' . $this->_conn->quote($video->video_id) . '"';
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'num_videos' => new Zend_Db_Expr('num_videos - 1'),
							),
							array(
								'playlist_id IN (?)' => new Zend_Db_Expr($playlistIds),
							));
		$this->_conn->delete($this->_prefix . 'media_video_playlist_assoc',
							array(
								'video_id = ?' => $video->video_id,
							));
		$this->_conn->delete($this->_prefix . 'media_video',
							array(
								'video_id = ?' => $video->video_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('v' => $this->_prefix . 'media_video'));
		if (isset($criteria['video_ids']) && !empty($criteria['video_ids'])) {
			$ids = is_array($criteria['video_ids']) ? implode(',', $criteria['video_ids']) : $criteria['video_ids']; 
			$select->where('v.video_id IN (?)', new Zend_Db_Expr($ids));
		}
		if (isset($criteria['playlist_id']) && !empty($criteria['playlist_id'])) {
			$select->joinLeft(array('vp' => $this->_prefix . 'media_video_playlist_assoc'), 'v.video_id = vp.video_id', array('ordering'))
				   ->where('vp.playlist_id = ?', $criteria['playlist_id'])
				   ->order('ordering DESC');
		}
		if (isset($criteria['tag']) && ($criteria['tag'] instanceof Tag_Models_Tag)) {
			$select->joinLeft(array('te' => $this->_prefix . 'tag_entity_assoc'), 'v.video_id = te.entity_id', array())
				   ->where('te.entity_class = ?', 'Media_Models_Video')
				   ->where('te.tag_id = ?', $criteria['tag']->tag_id);
		}
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('v.' . $key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("v.title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'v.video_id';
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
	 * @see Media_Models_Dao_Interface_Video::getById()
	 */
	public function getById($videoId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'media_video')
					->where('video_id = ?', $videoId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Media_Models_Video($row);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::increaseNumViews()
	 */
	public function increaseNumViews($video)
	{
		$this->_conn->update($this->_prefix . 'media_video',
							array(
								'num_views' => new Zend_Db_Expr('num_views + 1'),
							),
							array(
								'video_id = ?' => $video->video_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::removeFromPlaylist()
	 */
	public function removeFromPlaylist($videoId, $playlistId)
	{
		$this->_conn->delete($this->_prefix . 'media_video_playlist_assoc',
							array(
								'video_id = ?'	  => $videoId,
								'playlist_id = ?' => $playlistId,
							));
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'num_videos' => new Zend_Db_Expr('num_videos - 1'),
							),
							array(
								'playlist_id = ?' => $playlistId,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::rename()
	 */
	public function rename($video)
	{
		$this->_conn->update($this->_prefix . 'media_video',
							array(
								'title' => $video->title,
								'slug'	=> $video->slug,
							),
							array(
								'video_id = ?' => $video->video_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::update()
	 */
	public function update($video)
	{
		return $this->_conn->update($this->_prefix . 'media_video',
									array(
										'slug'			  => $video->slug,
										'title'			  => $video->title,
										'description'	  => $video->description,
										'image_square'	  => $video->image_square,
										'image_thumbnail' => $video->image_thumbnail,
										'image_small'	  => $video->image_small,
										'image_crop'	  => $video->image_crop,
										'image_medium'	  => $video->image_medium,
										'image_large'	  => $video->image_large,
										'image_original'  => $video->image_original,
										'url'			  => $video->url,
										'embed_code'	  => $video->embed_code,
										'duration'		  => $video->duration,
										'credit'		  => $video->credit,
										'language'		  => $video->language,
									),
									array(
										'video_id = ?' => $video->video_id,
									));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::updateStatus()
	 */
	public function updateStatus($video)
	{
		$this->_conn->update($this->_prefix . 'media_video',
							array(
								'status'		 => $video->status,
								'activated_date' => $video->activated_date,
							),
							array(
								'video_id = ?' => $video->video_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::updateUsername()
	 */
	public function updateUsername($user)
	{
		$this->_conn->update($this->_prefix . 'media_video',
							array(
								'user_name' => $user->user_name,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::deleteTag()
	 */
	public function deleteTag($tag)
	{
		// Do nothing
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::getTags()
	 */
	public function getTags($video)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('t' => $this->_prefix . 'tag'))
					   ->joinInner(array('te' => $this->_prefix . 'tag_entity_assoc'), 't.tag_id = te.tag_id', array())
					   ->where('te.entity_id = ?', $video->video_id)
					   ->where('te.entity_class = ?', get_class($video))
					   ->query()
					   ->fetchAll();
		$tags = array();
		foreach ($result as $row) {
			$tags[] = new Tag_Models_Tag($row);
		}
		return $tags;
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::setTags()
	 */
	public function setTags($video, $tags)
	{
		$class = get_class($video);
		$this->_conn->delete($this->_prefix . 'tag_entity_assoc',
							array(
								'entity_id = ?'	   => $video->video_id,
								'entity_class = ?' => $class,
							));
		if ($tags) {
			foreach ($tags as $tag) {
				$this->_conn->insert($this->_prefix . 'tag_entity_assoc',
									array(
										'tag_id'	   => $tag->tag_id,
										'entity_id'	   => $video->video_id,
										'entity_class' => $class,
									));
			}
		}
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::increaseNumComments()
	 */
	public function increaseNumComments($comment, $inc)
	{
		$numComments = 'num_comments' . $inc;
		$this->_conn->update($this->_prefix . 'media_video',
							array(
								'num_comments' => new Zend_Db_Expr($numComments),
							),
							array(
								'video_id = ?' => $comment->entity_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Video::increaseNumVotes()
	 */
	public function increaseNumVotes($vote)
	{
		$data = $vote->vote == 1
			  ? array('num_ups' => new Zend_Db_Expr('num_ups + 1'))
			  : array('num_downs' => new Zend_Db_Expr('num_downs + 1'));
		$this->_conn->update($this->_prefix . 'media_video',
							$data,
							array(
								'video_id = ?' => $vote->entity_id,
							));
	}
}
