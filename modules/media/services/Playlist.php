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

class Media_Services_Playlist
{
	/**
	 * Adds new playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return string Id of newly created playlist
	 */
	public static function add($playlist)
	{
		if (!$playlist || !($playlist instanceof Media_Models_Playlist)) {
			throw new Exception('The param is not an instance of Media_Models_Playlist');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Playlist',
								))
								->setDbConnection($conn)
								->add($playlist);
	}
	
	/**
	 * Counts the number of playlists that satisfies given searching conditions
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
									'name'	 => 'Playlist',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return bool
	 */
	public static function delete($playlist)
	{
		if (!$playlist || !($playlist instanceof Media_Models_Playlist) || $playlist->isNullOrEmpty($playlist->playlist_id)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Playlist',
								))
								->setDbConnection($conn)
								->delete($playlist);
		return true;
	}
	
	/**
	 * Finds playlists by given criteria
	 * 
	 * @param array $criteria An array consists of the following keys:
	 * - status: Playlist's status
	 * - title: Playlist's title
	 * - user_id: Id of user who created the playlist
	 * - sort_by: Name of field that you want to sort the result in. Default value is playlist_id
	 * - sort_dir: Sorting direction. Default is DESC
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
									'name'	 => 'Playlist',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets playlist instance by given Id
	 * 
	 * @param string $playlistId Playlist's Id
	 * @return Media_Models_Playlist|null
	 */
	public static function getById($playlistId)
	{
		if (!$playlistId || !is_string($playlistId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Playlist',
								))
								->setDbConnection($conn)
								->getById($playlistId);
	}
	
	/**
	 * Increases the number of views of given playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return bool
	 */
	public static function increaseNumViews($playlist)
	{
		if (!$playlist || !($playlist instanceof Media_Models_Playlist)) {
			throw new Exception('The param is not an instance of Media_Models_Playlist');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Playlist',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumViews($playlist);
		return true;
	}
	
	/**
	 * Renames the playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return bool
	 */
	public static function rename($playlist)
	{
		if (!$playlist || !($playlist instanceof Media_Models_Playlist)) {
			throw new Exception('The parameter is not an instance of Media_Models_Playlist');
		}
		if ($playlist->isNullOrEmpty($playlist->playlist_id) || $playlist->isNullOrEmpty($playlist->title)) {
			throw new Exception('The playlist Id or title has not been set');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Playlist',
								))
								->setDbConnection($conn)
								->rename($playlist);
		return true;
	}
	
	/**
	 * Updates the playlist's poster
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @param array $thumbnails Array of poster thumbnails
	 * @param Media_Models_Video $video The video instance
	 * @return bool
	 */
	public static function updatePoster($playlist, $thumbnails, $video = null)
	{
		if (!$playlist || !($playlist instanceof Media_Models_Playlist)) {
			throw new Exception('The first param is not an instance of Media_Models_Playlist');
		}
		if ($video && !($video instanceof Media_Models_Video)) {
			throw new Exception('The third param is not an instance of Media_Models_Video');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'	 => 'Playlist',
						 ))
						 ->setDbConnection($conn)
						 ->updatePoster($playlist, $thumbnails, $video);
		return true;		
	}
	
	/**
	 * Updates playlist's status
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return bool
	 */
	public static function updateStatus($playlist)
	{
		if (!$playlist || !($playlist instanceof Media_Models_Playlist)) {
			throw new Exception('The param is not an instance of Media_Models_Playlist');
		}
		if ($playlist->status == Media_Models_Playlist::STATUS_ACTIVATED) {
			$playlist->activated_date = date('Y-m-d H:i:s');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Playlist',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($playlist);
		
		// Execute hooks
		if ($playlist->status == Media_Models_Playlist::STATUS_ACTIVATED) {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$url  = $view->serverUrl() . $view->url($playlist->getProperties(), 'media_playlist_view');
			Core_Base_Hook_Registry::getInstance()->executeAction('Media_Activate_Playlist', array($playlist, $url));
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
							'name'   => 'Playlist',
						 ))
						 ->setDbConnection($conn)
						 ->updateUsername($user);
	}
}
