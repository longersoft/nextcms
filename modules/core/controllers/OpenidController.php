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

class Core_OpenidController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new OpenId URL
	 * 
	 * @return void
	 */
	public function addAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		$this->_helper->getHelper('layout')->disableLayout();
		
		Core_Services_Db::connect('master');
		
		$this->view->headTitle()->set($this->view->translator()->_('openid.add.title'));
		
		$request   = $this->getRequest();
		$storage   = new Core_Services_OpenIdStorage();
		$consumer  = new Core_Base_OpenId_Consumer($storage);
		$messenger = $this->_helper->getHelper('FlashMessenger');
		$returnUrl = $this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index') . '#u=' . $this->view->url(array(), 'core_openid_list');
		
		if ($request->isPost()) {
			$openIdUrl = $request->getPost('openid_url');
			if (!$consumer->login($openIdUrl)) {
				$messenger->addMessage($this->view->translator()->_('user._share.openIdUrlValidator'));
				$this->_redirect($returnUrl);
			}
		} else if ($request->getParam('openid_mode') == 'id_res') {
			if ($consumer->verify($_GET, $id)) {
				// Check if the OpenId URL is already used or not
				if ($id && ($users = Core_Services_User::getByOpenIdUrl($id)) && count($users) > 0) {
					$messenger->addMessage($this->view->translator()->_('user._share.openIdUrlInUse'));
				} else {
					$user = Zend_Auth::getInstance()->getIdentity();
					Core_Services_User::addOpenIdAssoc($user, $id);
					$messenger->addMessage($this->view->translator()->_('openid.add.success'));
				}
			} else {
				$messenger->addMessage($this->view->translator()->_('openid.add.error'));
			}
			$this->_redirect($returnUrl);
		}
	}
	
	/**
	 * Deletes given OpenId URL
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$format		= $request->getParam('format');
		$openIdUrl	= $request->getParam('openid_url');
		$user		= Zend_Auth::getInstance()->getIdentity();
		$openIdUrls = Core_Services_User::getOpenIdUrls($user);
		
		switch ($format) {
			case 'json':
				$result = false;
				if (in_array($openIdUrl, $openIdUrls)) {
					$result = Core_Services_User::deleteOpenIdAssoc($user, $openIdUrl);
				}
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'url'	 => $openIdUrl,
				));
				break;
			default:
				$openIdUrls = Core_Services_User::getOpenIdUrls($user);
				$this->view->assign(array(
					'invalidUrl' => !in_array($openIdUrl, $openIdUrls),
					'url'		 => $openIdUrl,
				));
				break;
		}
	}
	
	/**
	 * Lists OpenID URLs
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$user		= Zend_Auth::getInstance()->getIdentity();
		$openIdUrls = Core_Services_User::getOpenIdUrls($user);
		$this->view->assign('openIdUrls', $openIdUrls);
	}
}
