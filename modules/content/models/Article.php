<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	models
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Models_Article extends Core_Base_Models_Entity
{
	// Article's status
	// DO NOT CHANGES THESE VALUES
	const STATUS_DRAFT		   = 'draft';
	const STATUS_ACTIVATED	   = 'activated';
	const STATUS_NOT_ACTIVATED = 'not_activated';
	const STATUS_DELETED	   = 'deleted';

	// Article's type
	// DO NOT CHANGES THESE VALUES
	const TYPE_ARTICLE = 'article';
	const TYPE_PAGE	   = 'page';
	const TYPE_BLOG	   = 'blog';
	
	/**
	 * Array of status
	 * 
	 * @var array
	 */
	public static $STATUS = array(
		self::STATUS_DRAFT,
		self::STATUS_ACTIVATED,
		self::STATUS_NOT_ACTIVATED,
		self::STATUS_DELETED,
	);
	
	/**
	 * The url to view article's details
	 * 
	 * @var string
	 */
	private $_url = null;
	
	/**
	 * Article's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'article_id'	   => null,
		'category_id'	   => 0,
		'categories'	   => null,
		'type'			   => self::TYPE_ARTICLE,
		'title'			   => null,
		'sub_title'		   => null,
		'slug'			   => null,
		'description'	   => null,
		'meta_description' => null,
		'meta_keyword'	   => null,
		'content'		   => null,
		'layout'		   => null,
		'user_name'		   => null,
		'author'		   => null,
		'credit'		   => null,
		'featured'		   => 0,
		'image_icon'	   => 0,
		'video_icon'	   => 0,
		'ordering'		   => 0,
		'num_comments'	   => 0,
		'num_views'		   => 0,
		'image_square'	   => null,
		'image_thumbnail'  => null,
		'image_small'	   => null,
		'image_crop'	   => null,
		'image_medium'	   => null,
		'image_large'	   => null,
		'image_original'   => null,
		'cover_title'	   => null,
		'created_user'	   => null,
		'created_date'	   => null,
		'updated_user'	   => null,
		'updated_date'	   => null,
		'activated_user'   => null,
		'activated_date'   => null,
		'publishing_date'  => null,	// Use to publish the article in future automatically
		'status'		   => self::STATUS_NOT_ACTIVATED,
		'language'		   => null,
		'translations'	   => null,
	);
	
	/**
	 * @see Core_Base_Models_Entity::getId()
	 */
	public function getId()
	{
		return $this->_properties['article_id'];
	}
	
	/**
	 * @see Core_Base_Models_Entity::getTitle()
	 */
	public function getTitle()
	{
		return $this->_properties['title'];
	}
	
	/**
	 * @see Core_Base_Models_Entity::getProperties()
	 */
	public function getProperties($properties = null)
	{
		$pros = parent::getProperties($properties);
		
		// Allow to use {year}, {month}, {day} in article URL
		$date = $this->_properties['created_date'];
		if (null == $date) {
			$pros['year']  = date('Y');
			$pros['month'] = date('m');
			$pros['day']   = date('d');
		} else {
			$timestamp	   = strtotime($date);
			$pros['year']  = date('Y', $timestamp);
			$pros['month'] = date('m', $timestamp);
			$pros['day']   = date('d', $timestamp);
		}
		
		return $pros;
	}

	/**
	 * Gets the author's name which is used in the front-end
	 * 
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->_properties['author'] ? $this->_properties['author'] : $this->_properties['user_name'];
	}
	
	/**
	 * The shortcut method to get the full URL of cover in given size.
	 * The return value is used to set as value of the "src" attribute
	 * when render the cover of article:
	 * <img src="" />
	 * 
	 * @param string $size Size of cover. It can be one of "square", "thumbnail",
	 * "small", "crop", "medium", "large", "original"
	 * @return string
	 */
	public function getCover($size)
	{
		$thumb = $this->_properties['image_' . $size];
		if (!$thumb) {
			// FIXME: Return the default cover
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
	 * Gets array of cover thumbnails
	 * 
	 * @return array
	 */
	public function getCoverThumbnails()
	{
		$thumbnails = array();
		foreach (array('square', 'thumbnail', 'small', 'crop', 'medium', 'large', 'original') as $size) {
			$thumbnails[$size] = $this->_properties['image_' . $size];
		}
		return $thumbnails;
	}
	
	/**
	 * Gets the cover's title. It is used to set the value of the "title" attribute
	 * when render the cover using img tag:
	 * <img src="" title="" />
	 * 
	 * @return string
	 */
	public function getCoverTitle()
	{
		return $this->_properties['cover_title'] ? $this->_properties['cover_title'] : $this->_properties['title'];
	}
	
	/**
	 * Gets the article details URL
	 * 
	 * @return string
	 */
	public function getViewUrl()
	{
		// Cache the URL because the getViewUrl() method is used in the front-end template
		// more than one time
		if ($this->_url) {
			return $this->_url;
		}
		
		if (!isset($this->_properties['type'])) {
			$this->_properties['type'] = self::TYPE_ARTICLE;
		}
		$route = null;
		switch ($this->_properties['type']) {
			case self::TYPE_BLOG:
				$route = 'content_blog_view';
				break;
			case self::TYPE_PAGE:
				$route = 'content_page_view';
				break;
			case self::TYPE_ARTICLE:
			default:
				$route = 'content_article_view';
				break;
		}
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$this->_url = $view->serverUrl() . $view->url($this->getProperties(), $route);
		return $this->_url;
	}
}
