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

interface Media_Models_Dao_Interface_Album
{
	/**
	 * Adds new album
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @return string Id of newly created album
	 */
	public function add($album);
	
	/**
	 * Counts the number of albums that satisfies given searching conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes an album
	 * 
	 * @param Media_Models_Album $album
	 * @return void
	 */
	public function delete($album);
	
	/**
	 * Finds albums by given criteria
	 * 
	 * @param array $criteria An array consists of the following keys:
	 * - status: Album's status
	 * - title: Album's title
	 * - user_id: Id of user who created the album
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets album instance by given Id
	 * 
	 * @param string $albumId
	 * @return Media_Models_Album|null
	 */
	public function getById($albumId);
	
	/**
	 * Increases the number of views of album
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @return void
	 */
	public function increaseNumViews($album);
	
	/**
	 * Renames an album
	 * 
	 * @param Media_Models_Album $album
	 * @@return void
	 */
	public function rename($album);
	
	/**
	 * Updates the album's cover
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @param array $thumbnails Array of thumbnails
	 * @param Media_Models_Photo $photo The photo instance
	 * @return void
	 */
	public function updateCover($album, $thumbnails, $photo = null);
	
	/**
	 * Updates album's status
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @return void
	 */
	public function updateStatus($album);
	
	/**
	 * It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function updateUsername($user);
}
