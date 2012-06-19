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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_User
{
	/**
	 * Adds new user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return bool|string Id of newly created user
	 */
	public static function add($user)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		if (!$user->isValid() || self::isValidUsername($user->user_name) === false || self::isValidEmail($user->email) === false) {
			return false;
		}
		
		// Encrypt password
		$user->password = self::encryptPassword($user->password);
		
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'User',
								))
								->setDbConnection($conn)
								->add($user);
	}
	
	/**
	 * Maps given user with an OpenID URL
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param string $openIdUrl The OpenID URL
	 * @return bool
	 */
	public static function addOpenIdAssoc($user, $openIdUrl)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		if (!$openIdUrl) {
			return false;
		}
		$openIdUrl = rtrim($openIdUrl, '/');
		$conn	   = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'User',
						 ))
						 ->setDbConnection($conn)
						 ->addOpenIdAssoc($user, $openIdUrl);
		return true;
	}
	
	/**
	 * Gets the number of users who satisfy certain criteria
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'User',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function delete($user)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		if ($user->isRootUser()) {
			return false;
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'User',
						 ))
						 ->setDbConnection($conn)
						 ->delete($user);
		return true;
	}
	
	/**
	 * Deletes the association between user and OpenID URL
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param string $openIdUrl The OpenID URL
	 * @return bool
	 */
	public static function deleteOpenIdAssoc($user, $openIdUrl)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		if (!$openIdUrl) {
			return false;
		}
		$openIdUrl = rtrim($openIdUrl, '/');
		$conn	   = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'User',
						 ))
						 ->setDbConnection($conn)
						 ->deleteOpenIdAssoc($user, $openIdUrl);
		return true;
	}
	
	/**
	 * Encrypts user's password
	 * 
	 * @param string $password The user's password
	 * @return string
	 */
	public static function encryptPassword($password)
	{
		require_once 'PasswordHash.php';
		$hasher = new PasswordHash(8, true);
		return $hasher->HashPassword($password);
	}
	
	/**
	 * Finds user by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'User',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Generates random password
	 * 
	 * @param int $length The length of password
	 * @return string
	 */
	public static function generatePassword($length = 8)
	{
		return Core_Base_String::generateRandomString($length);
	}
	
	/**
	 * Gets user instance by given Id
	 * 
	 * @param string $userId User's Id
	 * @return Core_Models_User|null
	 */
	public static function getById($userId)
	{
		if (!$userId || !is_string($userId)) {
			return null;
		}
		
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'User',
								))
								->setDbConnection($conn)
								->getById($userId);
	}
	
	/**
	 * Gets user instance by given OpenId URL
	 * 
	 * @param string $openIdUrl The OpenID URL
	 * @return Core_Models_User|null
	 */
	public static function getByOpenIdUrl($openIdUrl)
	{
		if (!$openIdUrl || !is_string($openIdUrl)) {
			return null;
		}
		$openIdUrl = rtrim($openIdUrl, '/');
		$conn	   = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'User',
								))
								->setDbConnection($conn)
								->getByOpenIdUrl($openIdUrl);
	}
	
	/**
	 * Gets list of OpenId URLs associating with given user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return array
	 */
	public static function getOpenIdUrls($user)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'User',
									))
									->setDbConnection($conn)
									->getOpenIdUrls($user);
	}
	
	/**
	 * Checks if an email is valid when adding new user
	 * 
	 * @param string $email The email address
	 * @return bool|string Returns Id of user who was created by the email, otherwise returns true
	 */
	public static function isValidEmail($email)
	{
		if ($email == null || empty($email) || !is_string($email)) {
			return false;
		}
		
		// Check if the email address is valid using regular expression.
		// http://fightingforalostcause.net/misc/2006/compare-email-regex.php
		$pattern = '/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])){3}\])$/';
		if (!preg_match($pattern, $email)) {
			return false;
		}
		
		// There is other solution which uses Zend_Validate_EmailAddress
		//		$validator = new Zend_Validate_EmailAddress();
		//		$isValid   = $validator->isValid($email);
		
		$conn   = Core_Services_Db::getConnection();
		$result = Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'User',
								   ))
								   ->setDbConnection($conn)
								   ->isTakenEmail($email);
		return ($result == false) ? true : $result;
	}
	
	/**
	 * Checks if an username is valid or not
	 * 
	 * @param string $username The user name
	 * @return bool|string
	 */
	public static function isValidUsername($username)
	{
		if ($username == null || empty($username) || !is_string($username)) {
			return false;
		}
		
		// The valid user name can consist of a-z, A-Z, underscore (_), 0-9 digits
		$pattern = '/^[A-Za-z0-9_]+$/';
		// If you want to force the user name must start with an alphabet character,
		// then use the following pattern:
		//		$pattern = '/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/';
		if (!preg_match($pattern, $username)) {
			return false;
		}
		
		// Check if the user name is one of block user names
		$blockedUsernames = Core_Services_Config::get('core', 'register_blocked_usernames');
		if ($blockedUsernames) {
			$blockedUsernames = explode(',', $blockedUsernames);
			if (in_array($username, $blockedUsernames)) {
				return false;
			}
		}
		
		$conn   = Core_Services_Db::getConnection();
		$result = Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'User',
								   ))
								   ->setDbConnection($conn)
								   ->isTakenUsername($username);
		return ($result == false) ? true : $result;
	}
	
	/**
	 * Moves user to other group
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param Core_Models_Role $role The role instance
	 * @return bool
	 */
	public static function move($user, $role)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The first param is not an instance of Core_Models_User');
		}
		if ($role == null || !($role instanceof Core_Models_Role)) {
			throw new Exception('The second param is not an instance of Core_Models_Role');
		}
		if ($user->isRootUser()) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'User',
						 ))
						 ->setDbConnection($conn)
						 ->move($user, $role);
		return true;
	}
	
	/**
	 * Toggles active status of user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function toggleActiveStatus($user)
	{
		if ($user == null || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		if ($user->isRootUser()) {
			return false;
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'User',
						 ))
						 ->setDbConnection($conn)
						 ->toggleActiveStatus($user);
		return true;
	}
	
	/**
	 * Updates user's information
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function update($user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		$user->sanitize();
		
		// Get the current user info
		$conn		 = Core_Services_Db::getConnection();
		$userDao	 = Core_Services_Dao::factory(array(
											'module' => 'core',
											'name'   => 'User',
										))
										->setDbConnection($conn);
		$currentInfo = $userDao->getById($user->user_id);
		
		// If the password is empty then it will not be changed
		$user->password = ($user->password == '') ? $currentInfo->password : self::encryptPassword($user->password);
		
		// Update normal information
		$userDao->update($user);
		
		// If you want to change the group
		$newRoleId = $user->role_id;
		if (!$user->isRootUser() && $newRoleId != $currentInfo->role_id) {
			$roleDao = Core_Services_Dao::factory(array(
											'module' => 'core',
											'name'   => 'Role',
										))
										->setDbConnection($conn);
			$role = $roleDao->getById($newRoleId);
			$userDao->move($currentInfo, $role);
		}
		
		// Execute hooks
		if (!$user->isRootUser() && $user->user_name != $currentInfo->user_name) {
			Core_Base_Hook_Registry::getInstance()->executeAction('Core_Services_User_UpdateUsername', array($user));
		}
		
		return true;
	}
	
	/**
	 * Updates user's avatar
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return bool 
	 */
	public static function updateAvatar($user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		if (empty($user->avatar)) {
			return false;
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'User',
						 ))
						 ->setDbConnection($conn)
						 ->updateAvatar($user);
		return true;
	}
}
