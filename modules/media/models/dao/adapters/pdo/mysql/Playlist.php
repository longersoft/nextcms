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

class Media_Models_Dao_Adapters_Pdo_Mysql_Playlist extends Core_Base_Models_Dao
	implements Media_Models_Dao_Interface_Playlist
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Media_Models_Playlist($entity);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::add()
	 */
	public function add($playlist)
	{
		$this->_conn->insert($this->_prefix . 'media_playlist', 
							array(
								'title'		   => $playlist->title,
								'slug'		   => $playlist->slug,
								'created_date' => $playlist->created_date,
								'user_id'	   => $playlist->user_id,
								'user_name'	   => $playlist->user_name,
								'language'	   => $playlist->language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'media_playlist');
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'media_playlist', array('num_playlists' => 'COUNT(*)'));
		foreach (array('slug', 'user_id', 'status', 'language') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . '= ?', $criteria[$key]);
			}
		}
		if (isset($criteria['title']) && !empty($criteria['title'])) {
			$select->where("title LIKE '%" . addslashes($criteria['title']) . "%'");
		}
		return $select->limit(1)->query()->fetch()->num_playlists;
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::delete()
	 */
	public function delete($playlist)
	{
		$this->_conn->delete($this->_prefix . 'media_video_playlist_assoc',
							array(
								'playlist_id = ?' => $playlist->playlist_id,
							));
		return $this->_conn->delete($this->_prefix . 'media_playlist',
									array(
										'playlist_id = ?' => $playlist->playlist_id,
									));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'media_playlist');
		if (isset($criteria['playlist_ids']) && !empty($criteria['playlist_ids'])) {
			$ids = is_array($criteria['playlist_ids']) ? implode(',', $criteria['playlist_ids']) : $criteria['playlist_ids']; 
			$select->where('playlist_id IN (?)', new Zend_Db_Expr($ids));
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
			$criteria['sort_by'] = 'playlist_id';
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
	 * @see Media_Models_Dao_Interface_Playlist::getById()
	 */
	public function getById($playlistId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'media_playlist')
					->where('playlist_id = ?', $playlistId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Media_Models_Playlist($row);
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::increaseNumViews()
	 */
	public function increaseNumViews($playlist)
	{
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'num_views' => new Zend_Db_Expr('num_views + 1'),
							),
							array(
								'playlist_id = ?' => $playlist->playlist_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::rename()
	 */
	public function rename($playlist)
	{
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'title' => $playlist->title,
								'slug'	=> $playlist->slug,
							),
							array(
								'playlist_id = ?' => $playlist->playlist_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::updatePoster()
	 */
	public function updatePoster($playlist, $thumbnails, $video = null)
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
		if ($video) {
			$data['poster'] = $video->video_id;
		}
		$this->_conn->update($this->_prefix . 'media_playlist',
							$data,
							array(
								'playlist_id = ?' => $playlist->playlist_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::updateStatus()
	 */
	public function updateStatus($playlist)
	{
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'status'		 => $playlist->status,
								'activated_date' => $playlist->activated_date,
							),
							array(
								'playlist_id = ?' => $playlist->playlist_id,
							));
	}
	
	/**
	 * @see Media_Models_Dao_Interface_Playlist::updateUsername()
	 */
	public function updateUsername($user)
	{
		$this->_conn->update($this->_prefix . 'media_playlist',
							array(
								'user_name' => $user->user_name,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
}
