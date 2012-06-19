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

interface Media_Models_Dao_Interface_Photo
{
	/**
	 * Adds new photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return string Id of new photo
	 */
	public function add($photo);

	/**
	 * Adds a photo to album
	 * 
	 * @param string $photoId The photo's Id
	 * @param string $albumId The album's Id
	 * @param int $index Index of photo in the album
	 * @return void
	 */
	public function addToAlbum($photoId, $albumId, $index = 0);
	
	/**
	 * Gets the number of photos by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes a photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function delete($photo);
	
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
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets photo instance by given Flickr photo Id
	 * 
	 * @param string $flickrPhotoId The Flickr photo Id
	 * @return Media_Models_Photo|null
	 */
	public function getByFlickrId($flickrPhotoId);

	/**
	 * Gets photo instance by given Id
	 * 
	 * @param string $photoId The photo's Id
	 * @return Media_Models_Photo|null
	 */
	public function getById($photoId);
	
	/**
	 * Increases the number of downloads of photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function increaseNumDownloads($photo);
	
	/**
	 * Increases the number of views of photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function increaseNumViews($photo);
	
	/**
	 * Removes photo from album
	 * 
	 * @param string $photoId The photo's Id
	 * @param string $albumId The album's Id
	 * @return void
	 */
	public function removeFromAlbum($photoId, $albumId);
	
	/**
	 * Renames a photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function rename($photo);
	
	/**
	 * Updates basic information of photo, including title, description, photographer fields
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function update($photo);
	
	/**
	 * Updates photo's status
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function updateStatus($photo);
	
	/**
	 * It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function updateUsername($user);
	
	////////// MANAGE TAGS //////////
	
	/**
	 * Deletes association between photos and tags after removing a tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public function deleteTag($tag);
	
	/**
	 * Gets tags of given photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @return array
	 */
	public function getTags($photo);
	
	/**
	 * Sets tags to given photo
	 * 
	 * @param Media_Models_Photo $photo The photo instance
	 * @param array $tags Array of tags. Each item is an instance of Tag_Models_Tag
	 * @return void
	 */
	public function setTags($photo, $tags);
	
	////////// MANAGE COMMENTS //////////
	
	/**
	 * Increases or decreases the number of comments of given photo
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @param string $inc Can be "+1" or "-1"
	 * @return void
	 */
	public function increaseNumComments($comment, $inc);
	
	////////// MANAGE VOTES //////////

	/**
	 * Increases the number of vote ups/downs. It is called after user votes a given photo
	 * 
	 * @param Vote_Models_Vote $vote The vote instance
	 * @return void
	 */
	public function increaseNumVotes($vote);	
}
