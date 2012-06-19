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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Services_Video
{
	/**
	 * Adds new video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return string Id of newly created video
	 */
	public static function add($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The input parameter is not an instance of Media_Models_Video');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->add($video);
	}
	
	/**
	 * Adds a video to playlist
	 * 
	 * @param string $videoId Video's Id
	 * @param string $playlistId Playlist's Id
	 * @param int $index Index of video in the playlist
	 * @return bool
	 */
	public static function addToPlaylist($videoId, $playlistId, $index = 0)
	{
		if ($videoId == null || empty($videoId) || $playlistId == null || empty($playlistId)) {
			return false;
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->addToPlaylist($videoId, $playlistId, $index);
		return true;
	}
	
	/**
	 * Gets the number of videos by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (isset($criteria['title']) && $criteria['title']) {
			$criteria['title'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['title']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes a video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return bool
	 */
	public static function delete($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The input parameter is not an instance of Media_Models_Video');
		}
		if (!$video->video_id) {
			throw new Exception('The video Id has not been set');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->delete($video);
		
		// Delete thumbnails from the file system
		foreach (array('image_square', 'image_small', 'image_thumbnail', 'image_crop', 'image_medium', 'image_large', 'image_original') as $size) {
			$file = $video->$size;
			$file = str_replace('/', DS, $file);
			$file = APP_ROOT_DIR . DS . ltrim($file, DS);
			if (file_exists($file)) {
				@unlink($file);
			}
		}
		
		// Delete video
		if ($video->url) {
			$file = APP_ROOT_DIR . DS . ltrim($video->url, DS);
			if (file_exists($file)) {
				@unlink($file);
			}
		}
		
		return true;
	}
	
	/**
	 * Finds videos by a collection of conditions
	 * 
	 * @param array $criteria The array consists of matching conditions, including the following keys:
	 * - video_id
	 * - status
	 * - user_id
	 * - title
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		if (isset($criteria['title']) && $criteria['title']) {
			$criteria['title'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['title']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets video instance by given Id
	 * 
	 * @param string $videoId Video's Id
	 * @return Media_Models_Video|null
	 */
	public static function getById($videoId)
	{
		if (!$videoId || !is_string($videoId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->getById($videoId);
	}
	
	/**
	 * Removes video from playlist
	 * 
	 * @param string $videoId Video's Id
	 * @param string $playlistId Playlist's Id
	 * @return bool
	 */
	public static function removeFromPlaylist($videoId, $playlistId)
	{
		if ($videoId == null || empty($videoId) || $playlistId == null || empty($playlistId)) {
			throw new Exception('The playlist Id or video Id has not been set');
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->removeFromPlaylist($videoId, $playlistId);
		return true;
	}
	
	/**
	 * Renames the video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return bool
	 */
	public static function rename($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The parameter is not an instance of Media_Models_Video');
		}
		if ($video->isNullOrEmpty($video->video_id) || $video->isNullOrEmpty($video->title)) {
			throw new Exception('The video Id or title has not been set');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->rename($video);
		return true;
	}
	
	/**
	 * Updates given video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return bool
	 */
	public static function update($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The parameter is not an instance of Media_Models_Video');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Video',
								))
								->setDbConnection($conn)
								->update($video);
		return true;
	}
	
	/**
	 * Updates video's status
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return bool
	 */
	public static function updateStatus($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The param is not an instance of Media_Models_Video');
		}
		if ($video->status == Media_Models_Video::STATUS_ACTIVATED) {
			$video->activated_date = date('Y-m-d H:i:s');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($video);
		
		// Execute hooks
		if ($video->status == Media_Models_Video::STATUS_ACTIVATED) {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$url  = $view->serverUrl() . $view->url($video->getProperties(), 'media_video_view');
			Core_Base_Hook_Registry::getInstance()->executeAction('Media_Activate_Video', array($video, $url));
		}
		
		return true;
	}
	
	/**
	 * Updates the username field. It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public static function updateUsername($user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::connect('master');
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->updateUsername($user);
	}
	
	////////// MANAGE TAGS //////////
	
	/**
	 * Deletes association between videos and tags after removing a tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public static function deleteTag($tag)
	{
		if (!$tag || !($tag instanceof Tag_Models_Tag)) {
			throw new Exception('The param is not an instance of Tag_Models_Tag');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->deleteTag($tag);
	}
	
	/**
	 * Gets tags of given video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return array
	 */
	public static function getTags($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The param is not an instance of Media_Models_Video');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'   => 'Video',
								))
								->setDbConnection($conn)
								->getTags($video);
	}
	
	/**
	 * Increases the number of views of video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return bool
	 */
	public static function increaseNumViews($video)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The param is not an instance of Media_Models_Video');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumViews($video);
		return true;
	}
	
	/**
	 * Sets tags to given video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @param array $tags Array of tags. Each item is an instance of Tag_Models_Tag
	 * @return bool
	 */
	public static function setTags($video, $tags)
	{
		if (!$video || !($video instanceof Media_Models_Video)) {
			throw new Exception('The param is not an instance of Media_Models_Video');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->setTags($video, $tags);
		return true;
	}
	
	////////// MANAGE COMMENTS //////////
	
	/**
	 * Called after updating status of a comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return void
	 */
	public static function updateCommentStatus($comment)
	{
		if ($comment->entity_class != 'Media_Models_Video') {
			return;
		}
		$inc = ($comment->status == Comment_Models_Comment::STATUS_ACTIVATED) ? '+1' : '-1';
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumComments($comment, $inc);
	}
	
	////////// MANAGE VOTES //////////

	/**
	 * Increases the number of vote ups/downs. It is called after user votes a given video
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return void
	 */
	public static function increaseNumVotes($vote)
	{
		if (!$vote || !($vote instanceof Vote_Models_Vote)) {
			throw new Exception('The param is not an instance of Vote_Models_Vote');
		}
		if ($vote->entity_class != 'Media_Models_Video') {
			return;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Video',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumVotes($vote);
	}
}
