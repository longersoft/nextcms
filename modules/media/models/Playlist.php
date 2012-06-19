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

/**
 * Represents a playlist
 */
class Media_Models_Playlist extends Core_Base_Models_Entity 
{
	// Playlist's status
	// DO NOT CHANGE THESE VALUES
	const STATUS_ACTIVATED	   = 'activated';
	const STATUS_NOT_ACTIVATED = 'not_activated';
	
	/**
	 * Playlist's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'playlist_id'	  => null,
		'slug'			  => null,
		'title'			  => null,
		'description'	  => null,
		'created_date'	  => null,
		'user_id'		  => null,
		'user_name'		  => null,
		'num_views'		  => 0,
		'num_videos'	  => 0,
		'status'		  => self::STATUS_NOT_ACTIVATED,
		'activated_date'  => null,
		'poster'		  => null,		// Id of video that is choosen as the poster of the playlist
		'image_square'	  => null,
		'image_thumbnail' => null,
		'image_small'	  => null,
		'image_crop'	  => null,
		'image_medium'	  => null,
		'image_large'	  => null,
		'image_original'  => null,
		'language'		  => null,
	);
	
	/**
	 * @see Core_Base_Models_Entity::getId()
	 */
	public function getId()
	{
		return $this->_properties['playlist_id'];
	}
	
	/**
	 * @see Core_Base_Models_Entity::getTitle()
	 */
	public function getTitle()
	{
		return $this->_properties['title'];
	}
	
	/**
	 * Gets the author's name which is used in the front-end
	 * 
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->_properties['user_name'];
	}
	
	/**
	 * The shortcut method to get the full URL of poster in given size.
	 * 
	 * @param string $size Size of poster. It can be one of "square", "thumbnail",
	 * "small", "crop", "medium", "large", "original"
	 * @return string
	 */
	public function getPoster($size)
	{
		$thumb = $this->_properties['image_' . $size];
		if (!$thumb) {
			// FIXME: Return the default thumbnail
			return '';
		}
		if (substr($thumb, 0, 7) == 'http://') {
			return $thumb;
		} else {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			return rtrim($view->APP_ROOT_URL) . '/' . ltrim($thumb, '/');
		}
	}
}
