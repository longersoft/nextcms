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

class Media_Services_Album
{
	/**
	 * Adds new album
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @return string Id of newly created album
	 */
	public static function add($album)
	{
		if (!$album || !($album instanceof Media_Models_Album)) {
			throw new Exception('The param is not an instance of Media_Models_Album');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Album',
								))
								->setDbConnection($conn)
								->add($album);
	}
	
	/**
	 * Counts the number of albums that satisfies given searching conditions
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
									'name'	 => 'Album',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes an album
	 * 
	 * @param Media_Models_Album $album
	 * @return bool
	 */
	public static function delete($album)
	{
		if (!$album || !($album instanceof Media_Models_Album) || $album->isNullOrEmpty($album->album_id)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Album',
								))
								->setDbConnection($conn)
								->delete($album);
		return true;
	}
	
	/**
	 * Finds albums by given criteria
	 * 
	 * @param array $criteria An array consists of the following keys:
	 * - status: Album's status
	 * - title: Album's title
	 * - user_id: Id of user who created the album
	 * - sort_by: Name of field that you want to sort the result in. Default value is album_id
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
									'name'	 => 'Album',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets album instance by given Id
	 * 
	 * @param string $albumId
	 * @return Media_Models_Album|null
	 */
	public static function getById($albumId)
	{
		if (!$albumId || !is_string($albumId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Album',
								))
								->setDbConnection($conn)
								->getById($albumId);
	}
	
	/**
	 * Increases the number of views of album
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @return bool
	 */
	public static function increaseNumViews($album)
	{
		if (!$album || !($album instanceof Media_Models_Album)) {
			throw new Exception('The param is not an instance of Media_Models_Album');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Album',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumViews($album);
		return true;
	}
	
	/**
	 * Renames the album
	 * 
	 * @param Media_Models_Album $album
	 * @return bool
	 */
	public static function rename($album)
	{
		if (!$album || !($album instanceof Media_Models_Album)) {
			throw new Exception('The parameter is not an instance of Media_Models_Album');
		}
		if ($album->isNullOrEmpty($album->album_id) || $album->isNullOrEmpty($album->title)) {
			throw new Exception('The album Id or title has not been set');
		}
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
									'module' => 'media',
									'name'	 => 'Album',
								))
								->setDbConnection($conn)
								->rename($album);
		return true;
	}
	
	/**
	 * Updates the album's cover
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @param array $thumbnails Array of thumbnails
	 * @param Media_Models_Photo $photo The photo instance
	 * @return bool
	 */
	public static function updateCover($album, $thumbnails, $photo = null)
	{
		if (!$album || !($album instanceof Media_Models_Album)) {
			throw new Exception('The first param is not an instance of Media_Models_Album');
		}
		if ($photo && !($photo instanceof Media_Models_Photo)) {
			throw new Exception('The third param is not an instance of Media_Models_Photo');
		}
		
		$conn = Core_Services_Db::getConnection();
 		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'	 => 'Album',
						 ))
						 ->setDbConnection($conn)
						 ->updateCover($album, $thumbnails, $photo);
		return true;
	}
	
	/**
	 * Updates album's status
	 * 
	 * @param Media_Models_Album $album The album instance
	 * @return bool
	 */
	public static function updateStatus($album)
	{
		if (!$album || !($album instanceof Media_Models_Album)) {
			throw new Exception('The param is not an instance of Media_Models_Album');
		}
		if ($album->status == Media_Models_Album::STATUS_ACTIVATED) {
			$album->activated_date = date('Y-m-d H:i:s');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'media',
							'name'   => 'Album',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($album);
		
		// Execute hooks
		if ($album->status == Media_Models_Album::STATUS_ACTIVATED) {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$view = $viewRenderer->view;
			$url  = $view->serverUrl() . $view->url($album->getProperties(), 'media_album_view');
			Core_Base_Hook_Registry::getInstance()->executeAction('Media_Activate_Album', array($album, $url));
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
							'name'   => 'Album',
						 ))
						 ->setDbConnection($conn)
						 ->updateUsername($user);
	}
}
