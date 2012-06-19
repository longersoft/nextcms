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

class Media_Services_Photo
{
	/**
	 * Adds new photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return string Id of newly created photo
	 */
	public static function add($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The input parameter is not an instance of Media_Models_Photo');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->add($photo);
	}
	
	/**
	 * Adds a photo to album
	 * 
	 * @param string $photoId The photo's Id
	 * @param string $albumId The album's Id
	 * @param int $index Index of photo in the album
	 * @return bool
	 */
	public static function addToAlbum($photoId, $albumId, $index = 0)
	{
		if ($photoId == null || empty($photoId) || $albumId == null || empty($albumId)) {
			return false;
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->addToAlbum($photoId, $albumId, $index);
		return true;
	}
	
	/**
	 * Gets the number of photos by given criteria
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
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->count($criteria);
	}

	/**
	 * Deletes a photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function delete($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The input parameter is not an instance of Media_Models_Photo');
		}
		if (!$photo->photo_id) {
			throw new Exception('The photo Id has not been set');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->delete($photo);
		
		// Delete thumbnails from the file system
		foreach (array('image_square', 'image_small', 'image_thumbnail', 'image_crop', 'image_medium', 'image_large', 'image_original') as $size) {
			$file = $photo->$size;
			$file = str_replace('/', DS, $file);
			$file = APP_ROOT_DIR . DS . ltrim($file, DS);
			if (file_exists($file)) {
				@unlink($file);
			}
		}
		
		return true;
	}
	
	/**
	 * Finds photos by a collection of conditions
	 * 
	 * @param array $criteria The array consists of matching conditions, including the following keys:
	 * - album_id
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
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets photo instance by given Flickr photo Id
	 * 
	 * @param string $flickrPhotoId The Flickr photo Id
	 * @return Media_Models_Photo|null
	 */
	public static function getByFlickrId($flickrPhotoId)
	{
		if (!$flickrPhotoId || !is_string($flickrPhotoId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->getByFlickrId($flickrPhotoId);
	}
	
	/**
	 * Gets photo instance by given Id
	 * 
	 * @param string $photoId The photo's Id
	 * @return Media_Models_Photo|null
	 */
	public static function getById($photoId)
	{
		if (!$photoId || !is_string($photoId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->getById($photoId);
	}
	
	/**
	 * Imports photo from Flickr
	 * 
	 * @param array $data Contains the properties of Flickr photo:
	 * - flickr_id: Id of Flickr photo
	 * - title: The photo's title
	 * - thumbnails: Array of thumbnails
	 * @return bool
	 */
	public static function importFromFlickr($data)
	{
		@ini_set('max_execution_time', 0);
		
		$user		= Zend_Auth::getInstance()->getIdentity();
		$properties = array(
			'title'			=> $data['title'],
			'uploaded_date'	=> date('Y-m-d H:i:s'),
			'user_id'		=> $user->user_id,
		);
		
		// Download the photo
		$downloadDir = File_Services_Uploader::getUploadDir($user, 'media');
		$prefixUrl   = rtrim('/' . str_replace(DS, '/', ltrim($downloadDir, '/')), '/');
		Core_Base_File::createDirectories($downloadDir, APP_ROOT_DIR);
		
		$thumbs      = array();
		$thumbnails  = $data['thumbnails'];
		$url		 = '';
		$curlEnabled = extension_loaded('curl'); 
		
		foreach (array('square', 'thumbnail', 'small', 'crop', 'medium', 'large', 'original') as $size) {
			if (isset($thumbnails[$size])) {
				$fileName = pathinfo($thumbnails[$size]);
				$fileName = $fileName['basename'];
				$fp		  = fopen(APP_ROOT_DIR . DS . $downloadDir . DS . $fileName, 'w');
				
				if ($curlEnabled) {
					// Download file using CURL
					$ch = curl_init($thumbnails[$size]);
					curl_setopt($ch, CURLOPT_FILE, $fp);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_exec($ch);
					curl_close($ch);
				} else {
					$content = @file_get_contents($thumbnails[$size]);
					fwrite($fp, $content);
				}
				fclose($fp);
				
				$url = $prefixUrl . '/' . $fileName;
				$properties['image_' . $size] = $url;
			}
		}
		
		foreach (array('square', 'thumbnail', 'small', 'crop', 'medium', 'large', 'original') as $size) {
			if ($url && !isset($photo['image_' . $size])) {
				$properties['image_' . $size] = $url;
			}
		}
		
		$photo = self::getByFlickrId($data['flickr_id']);
		if ($photo) {
			foreach ($properties as $key => $value) {
				$photo->$key = $value;
			}
			self::update($photo);
		} else {
			$properties['flickr_id'] = $data['flickr_id'];
			// Add photo
			self::add(new Media_Models_Photo($properties));
		}
		
		return true;
	}
	
	/**
	 * Increases the number of downloads of photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function increaseNumDownloads($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The param is not an instance of Media_Models_Photo');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumDownloads($photo);
		return true;
	}
	
	/**
	 * Increases the number of views of photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function increaseNumViews($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The param is not an instance of Media_Models_Photo');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumViews($photo);
		return true;
	}
	
	/**
	 * Removes photo from album
	 * 
	 * @param string $photoId The photo's Id
	 * @param string $albumId The album's Id
	 * @return bool
	 */
	public static function removeFromAlbum($photoId, $albumId)
	{
		if ($photoId == null || empty($photoId) || $albumId == null || empty($albumId)) {
			throw new Exception('The album Id or photo Id has not been set');
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->removeFromAlbum($photoId, $albumId);
		return true;
	}
	
	/**
	 * Renames a photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function rename($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo) || $photo->isNullOrEmpty($photo->photo_id) 
			|| $photo->isNullOrEmpty($photo->title)) 
		{
			return false;
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->rename($photo);
		return true;
	}
	
	/**
	 * Updates basic information of photo, including title, description, photographer fields
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function update($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The input parameter is not an instance of Media_Models_Photo');
		}
		if ($photo->isNullOrEmpty($photo->photo_id) || $photo->isNullOrEmpty($photo->title)) {
			throw new Exception('The photo Id or title has not been set');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Photo',
								))
								->setDbConnection($conn)
								->update($photo);
		return true;
	}
	
	/**
	 * Updates photo's status
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function updateStatus($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The param is not an instance of Media_Models_Photo');
		}
		if ($photo->status == Media_Models_Photo::STATUS_ACTIVATED) {
			$photo->activated_date = date('Y-m-d H:i:s');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($photo);
		
		// Execute hooks
		if ($photo->status == Media_Models_Photo::STATUS_ACTIVATED) {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$url  = $view->serverUrl() . $view->url($photo->getProperties(), 'media_photo_view');
			Core_Base_Hook_Registry::getInstance()->executeAction('Media_Activate_Photo', array($photo, $url));
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
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->updateUsername($user);
	}
	
	////////// MANAGE TAGS //////////
	
	/**
	 * Deletes association between photos and tags after removing a tag
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
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->deleteTag($tag);
	}
	
	/**
	 * Gets tags of given photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return array
	 */
	public static function getTags($photo)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The param is not an instance of Media_Models_Photo');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'   => 'Photo',
								))
								->setDbConnection($conn)
								->getTags($photo);
	}
	
	/**
	 * Sets tags to given photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @param array $tags Array of tags. Each item is an instance of Tag_Models_Tag
	 * @return bool
	 */
	public static function setTags($photo, $tags)
	{
		if (!$photo || !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The param is not an instance of Media_Models_Photo');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->setTags($photo, $tags);
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
		if ($comment->entity_class != 'Media_Models_Photo') {
			return;
		}
		$inc = ($comment->status == Comment_Models_Comment::STATUS_ACTIVATED) ? '+1' : '-1';
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumComments($comment, $inc);
	}
	
	////////// MANAGE VOTES //////////

	/**
	 * Increases the number of vote ups/downs. It is called after user votes a given photo
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return void
	 */
	public static function increaseNumVotes($vote)
	{
		if (!$vote || !($vote instanceof Vote_Models_Vote)) {
			throw new Exception('The param is not an instance of Vote_Models_Vote');
		}
		if ($vote->entity_class != 'Media_Models_Photo') {
			return;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Photo',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumVotes($vote);
	}
}
