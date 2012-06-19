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
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_ErrorController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
		
	/**
	 * Deletes error
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$errorId = $request->getParam('error_id');
		$error	 = Core_Services_Error::getById($errorId);
		
		switch ($format) {
			case 'json':
				$result = Core_Services_Error::delete($error);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('error', $error);
				break;
		}
	}
	
	/**
	 * Lists errors
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		$q		 = $request->getParam('q');
		$default = array(
			'module'	=> null,
			'page'		=> 1,
			'per_page'	=> 20,
			'from_date' => null,
			'to_date'	=> null,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$errors	  = Core_Services_Error::find($criteria, $offset, $criteria['per_page']);
		$total	  = Core_Services_Error::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($errors, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		switch ($format) {
			case 'json':
				// Build data for the grid
				$paginatorTopic = $request->getParam('topic', '/app/core/error/list/onGotoPage');
				
				$items = array();
				foreach ($errors as $error) {
					$items[] = $error->getProperties();
				}
				$data = array(
					'errors'	=> array(
						'identifier' => 'error_id',
						'items'		 => $items,
					),
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('" . $paginatorTopic . "', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			default:
				// Get the list of modules
				$modules	= array();
				$moduleDirs = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
				foreach ($moduleDirs as $moduleName) {
					$file = APP_ROOT_DIR . DS . 'modules' . DS . $moduleName . DS . 'configs' . DS . 'about.php';
					if (!file_exists($file)) {
						continue;
					}
					$info	= include $file;
					$module = new Core_Models_Module(array(
						'name'		  => $info['name'],
						'title'		  => $info['title']['description'],
						'description' => $info['description']['description'],
					));
					$modules[] = array(
						'name'  => $module->name,
						'title' => $this->view->extensionTranslator()->translateTitle($module),
					);
				}
				
				$this->view->assign(array(
					'modules'  => Zend_Json::encode($modules),
					'criteria' => $criteria,
				));
				break;
		}
	}
	
	/**
	 * Views error details
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$errorId = $request->getParam('error_id');
		$error   = Core_Services_Error::getById($errorId);
		
		$this->view->assign('error', $error);
	}
}
