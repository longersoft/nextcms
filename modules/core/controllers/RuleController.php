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

class Core_RuleController extends Zend_Controller_Action
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
		$ajaxContext->addActionContext('role', array('html', 'json'))
					->addActionContext('user', array('html', 'json'))
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Sets role's permissions
	 * 
	 * @return void
	 */
	public function roleAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$roleId  = $request->getParam('role_id');
		$role	 = Core_Services_Role::getById($roleId);
		
		$this->view->assign('role', $role);
		
		switch ($format) {
			case 'html':
				$this->view->assign('module', $request->getParam('mod'));
				break;
			case 'json':
				$privileges = $request->getPost('privileges', array());
				$result		= Core_Services_Rule::setRolePermissions($role, $privileges);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('modules', Core_Services_Module::getInstalledModules());
				break;
		}
	}
	
	/**
	 * Sets user's permissions
	 * 
	 * @return void
	 */
	public function userAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$userId  = $request->getParam('user_id');
		$user	 = Core_Services_User::getById($userId);
		
		$this->view->assign('user', $user);
		
		switch ($format) {
			case 'html':
				$this->view->assign('module', $request->getParam('mod'));
				break;
			case 'json':
				$privileges = $request->getPost('privileges', array());
				$result		= Core_Services_Rule::setUserPermissions($user, $privileges);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('modules', Core_Services_Module::getInstalledModules());
				break;
		}
	}
}
