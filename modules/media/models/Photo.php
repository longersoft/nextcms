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
 * Represents a photo
 */
class Media_Models_Photo extends Core_Base_Models_Entity 
{
	// Photo's status
	// DO NOT CHANGE THESE VALUES
	const STATUS_ACTIVATED	   = 'activated';
	const STATUS_NOT_ACTIVATED = 'not_activated';
	
	/**
	 * Photo's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'photo_id'		  => null,
		'flickr_id'		  => null,
		'slug'			  => null,
		'title'			  => null,
		'description'	  => null,
		'image_square'	  => null,
		'image_thumbnail' => null,
		'image_small'	  => null,
		'image_crop'	  => null,
		'image_medium'	  => null,
		'image_large'	  => null,
		'image_original'  => null,
		'uploaded_date'	  => null,
		'user_id'		  => null,
		'user_name'		  => null,
		'photographer'	  => null,
		'num_comments'	  => 0,
		'num_views'		  => 0,
		'num_downloads'   => 0,
		'num_ups'		  => 0,
		'num_downs'		  => 0,
		'status'		  => self::STATUS_NOT_ACTIVATED,
		'activated_date'  => null,
		'language'		  => null,
	);
	
	/**
	 * @see Core_Base_Models_Entity::getId()
	 */
	public function getId()
	{
		return $this->_properties['photo_id'];
	}
	
	/**
	 * @see Core_Base_Models_Entity::getTitle()
	 */
	public function getTitle()
	{
		return $this->_properties['title'];
	}
	
	/**
	 * Gets the author's name to show in the front-end 
	 * 
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->_properties['photographer'] ? $this->_properties['photographer'] : $this->_properties['user_name']; 
	}
	
	/**
	 * The shortcut method to get the full URL of thumbnail in given size.
	 * The return value is used to set as value of the "src" attribute
	 * when render the thumbnail of a photo:
	 * <img src="" />
	 * 
	 * @param string $size Size of thumbnail. It can be one of "square", "thumbnail",
	 * "small", "crop", "medium", "large", "original"
	 * @return string
	 */
	public function getThumbnail($size)
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
	
	/**
	 * Gets array of photo's thumbnails
	 * 
	 * @return array
	 */
	public function getThumbnails()
	{
		$thumbnails = array();
		foreach (array('square', 'thumbnail', 'small', 'crop', 'medium', 'large', 'original') as $size) {
			$thumbnails[$size] = $this->_properties['image_' . $size];
		}
		return $thumbnails;
	}
}
