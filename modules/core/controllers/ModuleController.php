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

class Core_ModuleController extends Zend_Controller_Action
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
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Installs module
	 * 
	 * @return void
	 */
	public function installAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('name');
		$result	 = false;
		if ($module && strtolower($module) != 'core') {
			$result = Core_Services_Module::install($module) != null;
		}
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Lists modules
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'html':
				$this->view->assign('module', $request->getParam('mod'));
				break;
				
			default:
				// Get the list of modules
				$modules	= array();
				$moduleDirs = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
				
				$installedModules = array();
				foreach (Core_Services_Module::getInstalledModules() as $module) {
					$installedModules[] = $module->name;
				}
				
				foreach ($moduleDirs as $moduleName) {
					$file = APP_ROOT_DIR . DS . 'modules' . DS . $moduleName . DS . 'configs' . DS . 'about.php';
					if (!file_exists($file)) {
						continue;
					}
					$info	= include $file;
					$module = new Core_Models_Module(array(
						'name'			   => $info['name'],
						'title'			   => $info['title']['description'],
						'description'	   => $info['description']['description'],
						'thumbnail'		   => $this->view->APP_ROOT_URL . '/' . ltrim($info['thumbnail'], '/'),
						'website'		   => $info['website'],
						'author'		   => $info['author'],
						'email'			   => $info['email'],
						'version'		   => $info['version'],
						'app_version'	   => $info['appVersion'],
						'required_modules' => $info['requirements']['modules'],
						'license'		   => $info['license'],
						'is_installed'	   => in_array($info['name'], $installedModules),
					));
					$modules[] = $module;
				}
				
				$this->view->assign(array(
					'modules'		   => $modules,
					'installedModules' => $installedModules,
				));
				break;
		}
	}
	
	/**
	 * Uninstalls module
	 * 
	 * @return void
	 */
	public function uninstallAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('name');
		$result	 = Core_Services_Module::uninstall($module);
		$this->_helper->json(array(
			'result' => 'APP_RESULT_OK',
		));
	}
}
