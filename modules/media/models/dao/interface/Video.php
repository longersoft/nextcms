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

interface Media_Models_Dao_Interface_Video
{
	/**
	 * Adds new video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return string Id of newly created video
	 */
	public function add($video);
	
	/**
	 * Adds a video to playlist
	 * 
	 * @param string $videoId Video's Id
	 * @param string $playlistId Playlist's Id
	 * @param int $index Index of video in the playlist
	 * @return void
	 */
	public function addToPlaylist($videoId, $playlistId, $index = 0);
	
	/**
	 * Gets the number of videos by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes a video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return void
	 */
	public function delete($video);

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
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets video instance by given Id
	 * 
	 * @param string $videoId Video's Id
	 * @return Media_Models_Video|null
	 */
	public function getById($videoId);
	
	/**
	 * Increases the number of views of video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return void
	 */
	public function increaseNumViews($video);
	
	/**
	 * Removes video from playlist
	 * 
	 * @param string $videoId Video's Id
	 * @param string $playlistId Playlist's Id
	 * @return void
	 */
	public function removeFromPlaylist($videoId, $playlistId);
	
	/**
	 * Renames the video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return void
	 */
	public function rename($video);
	
	/**
	 * Updates given video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return void
	 */
	public function update($video);
	
	/**
	 * Updates video's status
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return void
	 */
	public function updateStatus($video);
	
	/**
	 * It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function updateUsername($user);
	
	////////// MANAGE TAGS //////////
	
	/**
	 * Deletes association between videos and tags after removing a tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public function deleteTag($tag);
	
	/**
	 * Gets tags of given video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @return array
	 */
	public function getTags($video);
	
	/**
	 * Sets tags to given video
	 * 
	 * @param Media_Models_Video $video The video instance
	 * @param array $tags Array of tags. Each item is an instance of Tag_Models_Tag
	 * @return void
	 */
	public function setTags($video, $tags);
	
	////////// MANAGE COMMENTS //////////
	
	/**
	 * Increases or decreases the number of comments of given video
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @param string $inc Can be "+1" or "-1"
	 * @return void
	 */
	public function increaseNumComments($comment, $inc);
	
	////////// MANAGE VOTES //////////

	/**
	 * Increases the number of vote ups/downs. It is called after user votes a given video
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return void
	 */
	public function increaseNumVotes($vote);
}
