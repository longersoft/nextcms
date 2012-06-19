<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a comment
 */
class Comment_Models_Comment extends Core_Base_Models_Entity
{
	// Comment's status
	// DO NOT CHANGE THESE VALUES
	const STATUS_ACTIVATED	   = 'activated';
	const STATUS_NOT_ACTIVATED = 'not_activated';
	const STATUS_SPAM		   = 'spam';
	
	/**
	 * Array of status
	 * 
	 * @var array
	 */
	public static $STATUS = array(
		self::STATUS_ACTIVATED,
		self::STATUS_NOT_ACTIVATED,
		self::STATUS_SPAM,
	);
	
	/**
	 * Comment's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'comment_id'	 => null,
		'entity_id'		 => null,
		'entity_class'	 => null,
		'entity_module'  => null,
		'title'			 => null,
		'content'		 => null,
		'full_name'		 => null,
		'web_site'		 => null,
		'email'			 => null,
		'ip'			 => null,
		'user_agent'	 => null,
		'created_user'	 => null,
		'created_date'	 => null,
		'activated_user' => null,
		'activated_date' => null,
		'status'		 => self::STATUS_NOT_ACTIVATED,
		'path'			 => '_',
		'ordering'		 => 0,
		'depth'			 => 0,
		'reply_to'		 => null,
		'language'		 => null,
		'num_ups'		 => 0,
		'num_downs'		 => 0,
	);
	
	/**
	 * @see Core_Base_Models_Entity::getId()
	 */
	public function getId()
	{
		return $this->_properties['comment_id'];
	}
	
	/**
	 * @see Core_Base_Models_Entity::getTitle()
	 */
	public function getTitle()
	{
		return $this->_properties['title'];
	}	
}
