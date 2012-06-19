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
 * @version		2012-03-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Media_Models_Dao_Interface_Playlist
{
	/**
	 * Adds new playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return string Id of newly created playlist
	 */
	public function add($playlist);
	
	/**
	 * Counts the number of playlists that satisfies given searching conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes given playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return void
	 */
	public function delete($playlist);
	
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
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets playlist instance by given Id
	 * 
	 * @param string $playlistId Playlist's Id
	 * @return Media_Models_Playlist|null
	 */
	public function getById($playlistId);
	
	/**
	 * Increases the number of views of given playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return void
	 */
	public function increaseNumViews($playlist);	
	
	/**
	 * Renames the playlist
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return void
	 */
	public function rename($playlist);
	
	/**
	 * Updates the playlist's poster
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @param array $thumbnails Array of poster thumbnails
	 * @param Media_Models_Video $video The video instance
	 * @return void
	 */
	public function updatePoster($playlist, $thumbnails, $video = null);
	
	/**
	 * Updates playlist's status
	 * 
	 * @param Media_Models_Playlist $playlist The playlist instance
	 * @return void
	 */
	public function updateStatus($playlist);
	
	/**
	 * It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function updateUsername($user);
}
