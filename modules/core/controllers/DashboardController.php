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

class Core_DashboardController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Shows user's dashboard
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		$user	   = Zend_Auth::getInstance()->getIdentity();
		$dashboard = Core_Services_Dashboard::getByUser($user);
		
		switch ($format) {
			case 'json':
				if ($dashboard) {
					$dashboard->layout = $request->getParam('layout');
					$result = Core_Services_Dashboard::update($dashboard);
				} else {
					Core_Services_Dashboard::add(new Core_Models_Dashboard(array(
						'user_id' => $user->user_id,
						'layout'  => $request->getParam('layout'),
					)));
					$result = true;
				}
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('layout', $dashboard ? $dashboard->layout : null);
				break;
		}
	}
}
