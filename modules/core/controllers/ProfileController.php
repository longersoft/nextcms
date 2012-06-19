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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_ProfileController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Updates user's profile
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request	 = $this->getRequest();
		$format		 = $request->getParam('format');
		$currentUser = Zend_Auth::getInstance()->getIdentity();
		
		switch ($format) {
			case 'json':
				$user = new Core_Models_User(array(
					'user_id'	=> $currentUser->user_id,
					'user_name'	=> $request->getPost('user_name'),
					'email'		=> $request->getPost('email'),
					'password'	=> $request->getPost('password'),
					'role_id'	=> $currentUser->role_id,
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
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				Zend_Locale::disableCache(true);
				$lang 	   = Core_Services_Config::get('core', 'language_code', 'en_US');
				$countries = Zend_Locale::getTranslationList('Territory', $lang, 2);
				$timeZones = Zend_Locale::getTranslationList('WindowsToTimezone', $lang);
				ksort($countries);
				ksort($timeZones);
				
				$this->view->assign(array(
					'user'		=> Core_Services_User::getById($currentUser->user_id),
					'countries' => $countries,
					'languages' => Zend_Locale::getTranslationList('Language', $lang),
					'timeZones' => $timeZones,
				));
				break;
		}
	}
}
