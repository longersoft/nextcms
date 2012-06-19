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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_User
{
	/**
	 * Adds new user. It also updates the number of users for the group that user belong to
	 * 
	 * @param Core_Models_User $user
	 * @return string Id of newly created user
	 */
	public function add($user);
	
	/**
	 * Maps given user with an OpenID URL
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param string $openIdUrl The OpenID URL
	 * @return void
	 */
	public function addOpenIdAssoc($user, $openIdUrl);
	
	/**
	 * Counts the number of users by given criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Deletes user
	 * 
	 * @param Core_Models_User $user
	 * @return void
	 */
	public function delete($user);
	
	/**
	 * Deletes the association between user and OpenID URL
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param string $openIdUrl The OpenID URL
	 * @return void
	 */
	public function deleteOpenIdAssoc($user, $openIdUrl);
	
	/**
	 * Searches for user
	 * 
	 * @param array $criteria The criteria can consist of the following keys:
	 * - role_id
	 * - email
	 * - user_name
	 * - sort_by: The name of user's properties that you want to sort the result
	 * The default value for this key is "user_id"
	 * - sort_dir: The sort direction, can be "asc" or "desc" (default)
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);

	/**
	 * Gets user instance by given Id
	 * 
	 * @param string $userId
	 * @return Core_Models_User|null
	 */
	public function getById($userId);
	
	/**
	 * Gets user instance by given OpenId URL
	 * 
	 * @param string $openIdUrl The OpenID URL
	 * @return Core_Models_User|null
	 */
	public function getByOpenIdUrl($openIdUrl);
	
	/**
	 * Gets list of OpenId URLs associating with given user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return array
	 */
	public function getOpenIdUrls($user);
	
	/**
	 * Checks if an email is already taken or not
	 * 
	 * @param string $email
	 * @return bool|string
	 */
	public function isTakenEmail($email);
	
	/**
	 * Checks if an username is already taken or not
	 * 
	 * @param string $username
	 * @return bool|string
	 */
	public function isTakenUsername($username);
	
	/**
	 * Moves user to other group
	 * 
	 * @param Core_Models_User $user
	 * @param Core_Models_Role $role
	 * @return void
	 */
	public function move($user, $role);
	
	/**
	 * Toggles active status of user
	 * 
	 * @param Core_Models_User $user
	 * @return void
	 */
	public function toggleActiveStatus($user);
	
	/**
	 * Updates normal information of user. It does not update the role of user
	 * 
	 * @param Core_Models_User $user
	 * @return void
	 */
	public function update($user);
	
	/**
	 * Updates user's avatar
	 * 
	 * @param Core_Models_User $user
	 * @return void 
	 */
	public function updateAvatar($user);
}
