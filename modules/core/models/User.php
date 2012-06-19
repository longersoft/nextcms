<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-31
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents an user
 */
class Core_Models_User extends Core_Base_Models_Entity 
{
	// The root admin. The app does not allow user to do most tasks to this user, such as activate,
	// set permissions, etc.
	// It is used to identify the user who have most hightest level of permissions.
	// DO NOT CHANGE THIS VALUE
	const ROOT_USER			   = 'admin';
	
	// User's status
	// DO NOT CHANGE THESE VALUES
	
	const STATUS_ACTIVATED	   = 'activated';
	const STATUS_NOT_ACTIVATED = 'not_activated';
	const STATUS_BANNED		   = 'banned';
	
	/**
	 * User's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'user_id'		 => null,
		'user_name'		 => null,
		'password'		 => null,
		'email'			 => null,
		'status'		 => self::STATUS_NOT_ACTIVATED,	// Defines user's status 	
		'created_date'	 => null,
		'logged_date'	 => null,
		'is_online'		 => 0,		// Online status. Can be 0 (offline) or 1 (online)
		'role_id'		 => null,
		'activation_key' => null,
		
		////////// Basic information for user's profile //////////
		// These information might be taken from openId account in future
		
		'full_name'		 => null,
		'avatar'		 => null, 
		'dob'			 => null,	// Date of birth
		'gender'		 => null,	// User's gender: m (male) or f (female)
		'website'		 => null,
		'bio'			 => null,
		'signature'		 => null,
		'country'		 => null,
		'language'		 => null,
		'timezone'		 => null,
		// Username on some popular social networks
		'twitter'		 => null,
		'facebook'		 => null,
		'flickr'		 => null,
		'youtube'		 => null,
		'linkedin'		 => null,
	);
	
	/**
	 * Simple validation of some required properties.
	 * The complicated tasks, such as checking existence of username and email, are performed in Service layer
	 * 
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->isNullOrEmpty($this->user_name) || $this->isNullOrEmpty($this->email)
			|| $this->isNullOrEmpty($this->password) || $this->isNullOrEmpty($this->role_id)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Checks if the user is root one
	 * 
	 * @return bool
	 */
	public function isRootUser()
	{
		return $this->user_name == self::ROOT_USER;
	}
}
