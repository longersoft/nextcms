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
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-10-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_UserController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Checks if an email is already taken or not
	 * 
	 * @return void
	 */
	public function checkemailAction()
	{
		Core_Services_Db::connect('master');
		
		$email  = $this->getRequest()->getParam('email');
		$result = Core_Services_User::isValidEmail($email);
		$this->_helper->json(array(
			'result' => $result,
		));
	}
	
	/**
	 * Checks if an username already exists or not
	 * 
	 * @return void
	 */
	public function checkusernameAction()
	{
		Core_Services_Db::connect('master');
		
		$username = $this->getRequest()->getParam('user_name');
		$result	  = Core_Services_User::isValidUsername($username);
		$this->_helper->json(array(
			'result' => $result,
		));
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates or deactivates user
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$userId  = $request->getPost('user_id');
		$user	 = Core_Services_User::getById($userId);
		$result  = Core_Services_User::toggleActiveStatus($user);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Adds new user
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$user = new Core_Models_User(array(
					'user_name'	   => $request->getPost('user_name'),
					'email'		   => $request->getPost('email'),
					'password'	   => $request->getPost('password'),
					'role_id'	   => $request->getPost('role_id'),
					'full_name'	   => $request->getPost('full_name'),
					'avatar'	   => $request->getPost('avatar'),
					'dob'		   => $request->getPost('dob', date('Y-m-d')),
					'gender'	   => $request->getPost('gender', 'm'),
					'website'	   => $request->getPost('website'),
					'bio'		   => $request->getPost('bio'),
					'signature'	   => $request->getPost('signature'),
					'country'	   => $request->getPost('country'),
					'language'	   => $request->getPost('language'),
					'timezone'	   => $request->getPost('timezone'),
					'created_date' => date('Y-m-d H:i:s'),
					'twitter'	   => $request->getPost('twitter'),
					'facebook'	   => $request->getPost('facebook'),
					'flickr'	   => $request->getPost('flickr'),
					'youtube'	   => $request->getPost('youtube'),
					'linkedin'	   => $request->getPost('linkedin'),
				));
				$userId = Core_Services_User::add($user);
				if ($userId === false) {
					$this->view->assign('user', null);
				} else {
					$user->user_id	  = $userId;
					$this->view->assign('user', $user);
				}
				$this->_helper->json(array(
					'result'  => 'APP_RESULT_OK',
					'status'  => $user->status,
					'role_id' => $user->role_id,
				));
				break;
				
			default:
				// Use Zend_Locale to populate the available timezones based on the language
				Zend_Locale::disableCache(true);
				$lang	   = Core_Services_Config::get('core', 'language_code', 'en_US');
				$countries = Zend_Locale::getTranslationList('Territory', $lang, 2);
				$timeZones = Zend_Locale::getTranslationList('WindowsToTimezone', $lang);
				ksort($countries);
				ksort($timeZones);
				
				$this->view->assign(array(
					'roles'			  => Core_Services_Role::find(),
					'countries'		  => $countries,
					'languages'		  => Zend_Locale::getTranslationList('Language', $lang),
					'timeZones'		  => $timeZones,
					'currentTimeZone' => Core_Services_Config::get('core', 'datetime_timezone'),
				));
				break;
		}
	}
	
	/**
	 * Updates avatar
	 * 
	 * @return void
	 */
	public function avatarAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$userId  = $request->getParam('user_id');
		$user		  = Core_Services_User::getById($userId);
		$user->avatar = $request->getParam('url');
		$result		  = Core_Services_User::updateAvatar($user);
		$this->_helper->json(array(
			'result'  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Deletes user
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$userId  = $request->getParam('user_id');
		$user	 = Core_Services_User::getById($userId);
		
		switch ($format) {
			case 'json':
				$result = Core_Services_User::delete($user);
				$this->_helper->json(array(
										'result'  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
										'user_id' => $userId,
										'role_id' => $user->role_id,
										'status'  => $user->status,
									));
				break;
			default:
				$this->view->assign('user', $user);
				break;
		}
	}
	
	/**
	 * Edits user information
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$user = new Core_Models_User(array(
					'user_id'	=> $request->getPost('user_id'),
					'user_name'	=> $request->getPost('user_name'),
					'email'		=> $request->getPost('email'),
					'password'	=> $request->getPost('password'),
					'role_id'	=> $request->getPost('role_id'),
					'full_name'	=> $request->getPost('full_name'),
					'avatar'	=> $request->getPost('avatar'),
					'dob'		=> $request->getPost('dob', date('Y-m-d')),
					'gender'	=> $request->getPost('gender', 'm'),
					'website'	=> $request->getPost('website'),
					'bio'		=> $request->getPost('bio'),
					'signature' => $request->getPost('signature'),
					'country'	=> $request->getPost('country'),
					'language'	=> $request->getPost('language'),
					'timezone'	=> $request->getPost('timezone'),
					'twitter' 	=> $request->getPost('twitter'),
					'facebook'	=> $request->getPost('facebook'),
					'flickr'	=> $request->getPost('flickr'),
					'youtube'	=> $request->getPost('youtube'),
					'linkedin'	=> $request->getPost('linkedin'),
				));
				Core_Services_User::update($user);
				$this->_helper->json(array(
										'result'	  => 'APP_RESULT_OK',
										'old_role_id' => $request->getPost('old_role_id'),
										'new_role_id' => $user->role_id,
									));
				break;
			default:
				$userId = $request->getParam('user_id');
				$user   = Core_Services_User::getById($userId);
				
				Zend_Locale::disableCache(true);
				$lang 	   = Core_Services_Config::get('core', 'language_code', 'en_US');
				$countries = Zend_Locale::getTranslationList('Territory', $lang, 2);
				$timeZones = Zend_Locale::getTranslationList('WindowsToTimezone', $lang);
				ksort($countries);
				ksort($timeZones);
				
				$this->view->assign(array(
					'user'		=> $user,
					'roles'		=> Core_Services_Role::find(),
					'countries' => $countries,
					'languages' => Zend_Locale::getTranslationList('Language', $lang),
					'timeZones' => $timeZones,
				));
				break;
		}
	}
	
	/**
	 * Lists users
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$q		  = $request->getParam('q');
		$default  = array(
			'page'	   => 1,
			'role_id'  => null,
			'status'   => null,
			'per_page' => 20,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		
		if (isset($criteria['keyword'])) {
			// Determine which field that user are searching for
			$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
			if (strpos($criteria['keyword'], '@') !== false && strpos($criteria['keyword'], '.') !== false && preg_match($chars, $criteria['keyword'])) {
				$criteria['email'] = $criteria['keyword'];
			} else {
				$criteria['user_name'] = $criteria['keyword'];
			}
		}
		
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$users	  = Core_Services_User::find($criteria, $offset, $criteria['per_page']);
		$total	  = Core_Services_User::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($users, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'users'		=> $users,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Moves user to other group
	 * 
	 * @return void
	 */
	public function moveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$userId  = $request->getPost('user_id');
		$roleId  = $request->getPost('role_id');
		$user	 = Core_Services_User::getById($userId);
		$role	 = Core_Services_Role::getById($roleId);
		$result  = Core_Services_User::move($user, $role);
		$this->_helper->json(array(
								'result'	  => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
								'user_id'	  => $userId,
								'old_role_id' => $user->role_id,
								'new_role_id' => $roleId,
							));
	}
}
