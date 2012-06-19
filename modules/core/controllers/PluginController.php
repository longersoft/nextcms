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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_PluginController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Configs a plugin
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$request = $this->getRequest();
		$module	 = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$format  = $request->getParam('format');
		
		$class	 = ucfirst(strtolower($module)) . '_Plugins_' . ucfirst(strtolower($name)) . '_Plugin';
		$plugin  = new $class();
		
		switch ($format) {
			case 'json':
				$result = $plugin->save(array(
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
					'configBody' => $plugin->config(),
				));
				break;
		}
	}
	
	/**
	 * Disables a plugin
	 * 
	 * @return void
	 */
	public function disableAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Plugin::enable($name, $module, false);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Enables a plugin
	 * 
	 * @return void
	 */
	public function enableAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Plugin::enable($name, $module, true);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Installs a plugin
	 * 
	 * @return void
	 */
	public function installAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Plugin::install($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Lists plugins
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
				
		$installedPlugins = array();
		foreach (Core_Services_Plugin::getInstalledPlugins() as $plugin) {
			$installedPlugins[$plugin->module . '_' . $plugin->name] = $plugin;
		}
		
		$plugins = array();
		$dirs	 = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'plugins');
		foreach ($dirs as $dir) {
			$plugin = Core_Services_Plugin::getPluginInstance($dir, $module);
			if ($plugin) {
				$plugin->thumbnail	  = $this->view->APP_ROOT_URL . '/' . ltrim($plugin->thumbnail, '/');
				$plugin->is_installed = isset($installedPlugins[$plugin->module . '_' . $plugin->name]);
				$plugin->enabled      = isset($installedPlugins[$plugin->module . '_' . $plugin->name]) && ($installedPlugins[$plugin->module . '_' . $plugin->name]->enabled == '1');
				
				$plugins[] = $plugin;
			}
		}
		
		$this->view->assign('plugins', $plugins);
	}
	
	/**
	 * Uninstalls a plugin
	 * 
	 * @return void
	 */
	public function uninstallAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module	 = $request->getPost('mod');
		$name	 = $request->getPost('name');
		$result	 = Core_Services_Plugin::uninstall($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
