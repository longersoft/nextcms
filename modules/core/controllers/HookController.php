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

class Core_HookController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Configures a hook
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$request = $this->getRequest();
		$module	 = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$format  = $request->getParam('format');
		
		$class	 = ucfirst(strtolower($module)) . '_Hooks_' . ucfirst(strtolower($name)) . '_Hook';
		$hook	 = new $class();
		
		switch ($format) {
			case 'json':
				$result = $hook->save(array(
					'noRenderScript' => true,
				));
				$this->_helper->json(array(
					'result' => ($result == 'true') ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'module'	 => $module,
					'name'		 => $name,
					'configBody' => $hook->config(),
				));
				break;
		}
	}
	
	/**
	 * Installs a hook
	 * 
	 * @return void
	 */
	public function installAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Hook::install($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Lists hooks
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
				
		$installedHooks = array();
		foreach (Core_Services_Hook::getInstalledHooks() as $hook) {
			$installedHooks[] = $hook->module . '_' . $hook->name;
		}
		
		$hooks = array();
		$dirs  = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'hooks');
		foreach ($dirs as $dir) {
			$hook = Core_Services_Hook::getHookInstance($dir, $module);
			if ($hook) {
				$hook->thumbnail	= $this->view->APP_ROOT_URL . '/' . ltrim($hook->thumbnail, '/');
				$hook->is_installed = in_array($hook->module . '_' . $hook->name, $installedHooks);
				
				$hooks[] = $hook;
			}
		}
		
		$this->view->assign('hooks', $hooks);
	}
	
	/**
	 * Uninstalls a hook
	 * 
	 * @return void
	 */
	public function uninstallAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Hook::uninstall($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
