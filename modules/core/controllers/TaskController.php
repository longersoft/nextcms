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

class Core_TaskController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Configures task
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$format  = $request->getParam('format');
		
		$class	 = ucfirst(strtolower($module)) . '_Tasks_' . ucfirst(strtolower($name)) . '_Task';
		$task	 = new $class();
		
		switch ($format) {
			case 'json':
				$result = $task->save(array(
					'noRenderScript' => true,
				));
				$this->_helper->json(array(
					'result' => ($result == 'true') ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'name'	 => $name,
				));
				break;
			default:
				$this->view->assign(array(
					'module'	 => $module,
					'name'		 => $name,
					'configBody' => $task->config(),
				));
				break;
		}
	}
	
	/**
	 * Installs task
	 * 
	 * @return void
	 */
	public function installAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$result	 = Core_Services_Task::install($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'name'	 => $name,
		));
	}
	
	/**
	 * Lists tasks
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
			'module' => null,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		
		switch ($format) {
			case 'json':
				// Build data for the grid
				$items = array();
				
				$installedTasks = array();
				foreach (Core_Services_Task::getInstalledTasks(array('module' => $criteria['module'])) as $task) {
					$installedTasks[$task->module . '_' . $task->name] = $task;
				}
				
				$modules = $criteria['module'] ? array($criteria['module']) : Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
				foreach ($modules as $module) {
					$taskDirs = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'tasks');
					foreach ($taskDirs as $dir) {
						$task = Core_Services_Task::getTaskInstance($dir, $module);
						if ($task) {
							$installedTask = isset($installedTasks[$task->module . '_' . $task->name]) 
											? $installedTasks[$task->module . '_' . $task->name]
											: null;
							$actions = null;
							if ($task->actions) {
								$actions = array();
								foreach ($task->actions as $key => $value) {
									$actions[$key] = array_merge($value, array(
										'allowed' => Core_Services_Acl::getInstance()->has('task:' . $task->module . ':' . $task->name)
													? true
													: Core_Services_Acl::getInstance()->isUserOrRoleAllowed($task->module, $task->name, $key, 'task'),
									));
								}
								
								$actions = Zend_Json::encode($actions);
							}
							
							$items[]	   = array(
								'task_identify'  => $task->module . '_' . $task->name,
								'module'	     => $task->module,
								'name'		     => $task->name,
								'title'		     => $this->view->extensionTranslator()->translateTitle($task),
								'description'    => $this->view->extensionTranslator()->translateDescription($task),
								'last_run'		 => ($installedTask && $installedTask->last_run) ? date('Y-m-d H:i:s', $installedTask->last_run) : null,
								'next_run'		 => ($installedTask && $installedTask->next_run) ? date('Y-m-d H:i:s', $installedTask->next_run) : null,
								'is_installed'   => $installedTask ? true : false,
								'is_configuable' => $task->options === null ? false : true,
								'actions'		 => $actions,
							);
						}
					}
				}
				
				$this->_helper->json(array(
					'data' => array(
						'identifier' => 'task_identify',
						'items'		 => $items,
					),
				));
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
	 * Runs a task
	 * 
	 * @return void
	 */
	public function runAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$result  = false;
		
		// Check if the task is installed
		$tasks = Core_Services_Task::getInstalledTasks(array(
			'module' => $module,
			'name'	 => $name,
		));
		if ($tasks == null || count($tasks) == 0) {
			$result = false;
		} else {
			// Execute the task
			$result = Core_Services_Task::execute($tasks[0]);
		}
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'name'	 => $name,
		));
	}
	
	/**
	 * Schedules task
	 * 
	 * @return void
	 */
	public function scheduleAction()
	{
		require_once 'CronExpression/CronExpression.php';
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$mask = array(
					$request->getPost('minute'),
					$request->getPost('hour'),
					$request->getPost('day'),
					$request->getPost('month'),
					$request->getPost('weekday'),
				);
				$task = new Core_Models_Task(array(
					'module'	=> $module,
					'name'		=> $name,
					'time_mask' => implode(' ', $mask),
				));
				Core_Services_Task::updateTimeMask($task);
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$tasks = Core_Services_Task::getInstalledTasks(array(
							'module' => $module,
							'name'	 => $name,
						));
				$task  = count($tasks) == 0 ? null : $tasks[0];
				$mask  = array(
					'minute'  => '*',
					'hour'	  => '*',
					'day'	  => '*',
					'month'	  => '*',
					'weekday' => '*',
				);
				if ($task) {
					$timeMask = CronExpression_CronExpression::factory($task->time_mask);
					$mask = array(
						'minute'  => $timeMask->getExpression(CronExpression_CronExpression::MINUTE),
						'hour'	  => $timeMask->getExpression(CronExpression_CronExpression::HOUR),
						'day'	  => $timeMask->getExpression(CronExpression_CronExpression::DAY),
						'month'	  => $timeMask->getExpression(CronExpression_CronExpression::MONTH),
						'weekday' => $timeMask->getExpression(CronExpression_CronExpression::WEEKDAY),
					);
				}
				
				$this->view->assign(array(
					'task'	   => $task,
					'timeMask' => $mask,
				));
				break;
		}
	}
	
	/**
	 * Uninstalls task
	 * 
	 * @return void
	 */
	public function uninstallAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$module  = $request->getParam('mod');
		$name	 = $request->getParam('name');
		$result	 = Core_Services_Task::uninstall($name, $module);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'name'	 => $name,
		));
	}
}
