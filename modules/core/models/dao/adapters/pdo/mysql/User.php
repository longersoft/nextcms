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
 * @version		2012-03-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_User extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_User
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_User($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::add()
	 */
	public function add($user)
	{
		$this->_conn->update($this->_prefix . 'core_role',
							array(
								'num_users' => new Zend_Db_Expr('num_users + 1'),
							),
							array(
								'role_id = ?' => $user->role_id,
							));
		
		$this->_conn->insert($this->_prefix . 'core_user',
							array(
								'role_id'	   => $user->role_id,
								'user_name'	   => $user->user_name,
								'email'		   => $user->email,
								'password'	   => $user->password,
								'status'	   => $user->status,
								'created_date' => $user->created_date,
								'full_name'	   => $user->full_name,
								'avatar'	   => $user->avatar,
								'dob'		   => $user->dob,
								'gender'	   => $user->gender,
								'website'	   => $user->website,
								'bio'		   => $user->bio,
								'signature'	   => $user->signature,
								'twitter'	   => $user->twitter,
								'facebook'	   => $user->facebook,
								'flickr'	   => $user->flickr,
								'youtube'	   => $user->youtube,
								'linkedin'	   => $user->linkedin,
								'country'	   => $user->country,
								'language'	   => $user->language,
								'timezone'	   => $user->timezone,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_user');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::addOpenIdAssoc()
	 */
	public function addOpenIdAssoc($user, $openIdUrl)
	{
		$this->_conn->insert($this->_prefix . 'core_openid_user_assoc',
							array(
								'user_id'	 => $user->user_id,
								'openid_url' => $openIdUrl,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_user', array('num_users' => 'COUNT(*)'));
		foreach (array('user_name', 'email', 'activation_key', 'role_id', 'status') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		return $select->limit(1)->query()->fetch()->num_users;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::delete()
	 */
	public function delete($user)
	{
		$this->_conn->update($this->_prefix . 'core_role',
							array(
								'num_users' => new Zend_Db_Expr('num_users - 1'),
							),
							array(
								'role_id = ?' => $user->role_id,
							));
		$this->_conn->delete($this->_prefix . 'core_user',
							array(
								'user_id = ?' => $user->user_id,
							));
		$this->_conn->delete($this->_prefix . 'core_openid_user_assoc',
							array(
								'user_id = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::deleteOpenIdAssoc()
	 */
	public function deleteOpenIdAssoc($user, $openIdUrl)
	{
		$this->_conn->delete($this->_prefix . 'core_openid_user_assoc',
							array(
								'user_id = ?'	 => $user->user_id,
								'openid_url = ?' => $openIdUrl,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_user');
		foreach (array('user_name', 'email', 'activation_key', 'role_id', 'status') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'user_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::getById()
	 */
	public function getById($userId)
	{
		$row = $this->_conn
					->select()
					->from(array('u' => $this->_prefix . 'core_user'))
					->joinLeft(array('r' => $this->_prefix . 'core_role'), 'u.role_id = r.role_id', array('role_name' => 'name'))
					->where('user_id = ?', $userId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_User($row);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::getByOpenIdUrl()
	 */
	public function getByOpenIdUrl($openIdUrl)
	{
		$row = $this->_conn
					->select()
					->from(array('u' => $this->_prefix . 'core_user'))
					->joinLeft(array('r' => $this->_prefix . 'core_role'), 'u.role_id = r.role_id', array('role_name' => 'name'))
					->joinInner(array('uo' => $this->_prefix . 'core_openid_user_assoc'), 'u.user_id = uo.user_id', array())
					->where('uo.openid_url = ?', $openIdUrl)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_User($row);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::getOpenIdUrls()
	 */
	public function getOpenIdUrls($user)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('u' => $this->_prefix . 'core_user'), array())
					   ->joinInner(array('uo' => $this->_prefix . 'core_openid_user_assoc'), 'u.user_id = uo.user_id', array('openid_url'))
					   ->where('u.user_id = ?', $user->user_id)
					   ->query()
					   ->fetchAll();
		$openIdUrls = array();
		foreach ($result as $row) {
			$openIdUrls[] = $row->openid_url;
		}
		return $openIdUrls;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::isTakenEmail()
	 */
	public function isTakenEmail($email)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_user', array('user_id'))
					->where('email = ?', $email)
					->limit(1)
					->query()
					->fetch();
		return ($row == null) ? false : $row->user_id;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::isTakenUsername()
	 */
	public function isTakenUsername($username)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_user', array('user_id'))
					->where('user_name = ?', $username)
					->limit(1)
					->query()
					->fetch();
		return ($row == null) ? false : $row->user_id;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::move()
	 */
	public function move($user, $role)
	{
		$this->_conn->update($this->_prefix . 'core_role',
							array(
								'num_users' => new Zend_Db_Expr('num_users + 1'),
							),
							array(
								'role_id = ?' => $role->role_id,
							));
		$this->_conn->update($this->_prefix . 'core_role',
							array(
								'num_users' => new Zend_Db_Expr('num_users - 1'),
							),
							array(
								'role_id = ?' => $user->role_id,
							));
		$this->_conn->update($this->_prefix . 'core_user',
							array(
								'role_id' => $role->role_id,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::toggleActiveStatus()
	 */
	public function toggleActiveStatus($user)
	{
		$status = ($user->status == Core_Models_User::STATUS_ACTIVATED) 
					? Core_Models_User::STATUS_NOT_ACTIVATED : Core_Models_User::STATUS_ACTIVATED;
		$this->_conn->update($this->_prefix . 'core_user',
							array(
								'status' => $status,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::update()
	 */
	public function update($user)
	{
		$this->_conn->update($this->_prefix . 'core_user',
							array(
								'user_name'		 => $user->user_name,
								'email' 		 => $user->email,
								'password'		 => $user->password,
								'activation_key' => $user->activation_key,
								'full_name'		 => $user->full_name,
								'avatar'		 => $user->avatar,
								'dob'			 => $user->dob,
								'gender'		 => $user->gender,
								'website'		 => $user->website,
								'bio'			 => $user->bio,
								'signature'		 => $user->signature,
								'twitter'		 => $user->twitter,
								'facebook'		 => $user->facebook,
								'flickr'		 => $user->flickr,
								'youtube'		 => $user->youtube,
								'linkedin'		 => $user->linkedin,
								'country'		 => $user->country,
								'language'		 => $user->language,
								'timezone'		 => $user->timezone,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_User::updateAvatar()
	 */
	public function updateAvatar($user)
	{
		$this->_conn->update($this->_prefix . 'core_user',
							array(
								'avatar' => $user->avatar,
							),
							array(
								'user_id = ?' => $user->user_id,
							));
	}
}
