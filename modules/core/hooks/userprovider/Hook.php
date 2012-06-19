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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Hooks_Userprovider_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_core', array($this, 'menu'), true);
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/modules/core/hooks/userprovider/styles.css');
		}
	}
	
	/**
	 * Shows the menu item in the back-end
	 * 
	 * @return void
	 */
	public function menuAction()
	{
	}
	
	/**
	 * Searches for users
	 * 
	 * @return void
	 */
	public function searchAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$q		  = $request->getParam('q');
		$default  = array(
			'page'	   => 1,
			'status'   => null,
			'per_page' => 20,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		if (isset($criteria['keyword'])) {
			// Determine which field that user are searching for
			$pattern = '/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i';
			if (strpos($criteria['keyword'], '@') !== false && strpos($criteria['keyword'], '.') !== false && preg_match($pattern, $criteria['keyword'])) {
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
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Shows a toolbox attached to the main Toolbox
	 * 
	 * @return void
	 */
	public function showAction()
	{
	}
}
