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

class Core_RoleController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Adds new role
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
				$role	= new Core_Models_Role(array(
								'description' => $request->getPost('name'),
								'locked'	  => 0,
								'num_users'	  => 0,
							));
				$roleId = Core_Services_Role::add($role);
				$this->_helper->json(array(
					'result'  => 'APP_RESULT_OK',
					'role_id' => $roleId,
				));
				break;
			default:
				break;
		}
	}
	
	/**
	 * Deletes role
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$roleId  = $request->getParam('role_id');
		$role	 = Core_Services_Role::getById($roleId);
		
		switch ($format) {
			case 'json':
				$result = Core_Services_Role::delete($role);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('role', $role);
				break;
		}
	}
	
	/**
	 * Lists roles
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$q		  = $request->getParam('q');
		$default  = array(
			'page'			 => 1,
			'locked'		 => null,
			'active_role_id' => null,		// To show the selected role
			'per_page'		 => 10,
			'name'			 => null,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$roles	  = Core_Services_Role::find($criteria, $offset, $criteria['per_page']);
		$total	  = Core_Services_Role::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($roles, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'roles'		=> $roles,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Locks or unlocks role
	 * 
	 * @return void
	 */
	public function lockAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$roleId  = $request->getPost('role_id');
		$role	 = Core_Services_Role::getById($roleId);
		$result  = Core_Services_Role::toggleLock($role);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Renames role
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$role	 = new Core_Models_Role(array(
						'role_id'	  => $request->getPost('role_id'),
						'description' => $request->getPost('name'),
					));
		$result  = Core_Services_Role::rename($role);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
