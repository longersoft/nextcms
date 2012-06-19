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
 * @subpackage	services
 * @since		1.0
 * @version		2011-12-31
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Task
{
	/**
	 * Executes a given task
	 * 
	 * @param Core_Models_Task $task The task instance
	 * @return bool
	 */
	public static function execute($task)
	{
		if ($task == null || !($task instanceof Core_Models_Task)) {
			throw new Exception('The param is not an instance of Core_Models_Task');
		}
		$class = ucfirst($task->module) . '_Tasks_' . ucfirst($task->name) . '_Task';
		if (!class_exists($class)) {
			return false;
		}
		$taskInstance = new $class();
		if (!($taskInstance instanceof Core_Base_Extension_Task)) {
			return false;
		}
		
		// Calculate the next execution time
		if ($task->last_run == null) {
			$task->last_run = time() + 30;
		}
		
		require_once 'CronExpression/CronExpression.php';
		$expression  = CronExpression_CronExpression::factory($task->time_mask);
		$nextRunTime = $expression->getNextRunDate(date('Y-m-d H:i:s', $task->last_run));
		$nextRunTime = strtotime($nextRunTime->format('Y-m-d H:i:s'));
		
		if ($task->next_run == null || $nextRunTime >= $task->next_run) {
			$task->next_run = $nextRunTime;
			// Update the time of last and next execution
			Core_Services_Task::updateExecutionTimes($task);
		}
		
		if ($nextRunTime >= $task->next_run) {
			// Execute the task
			$taskInstance->execute();
		}
		
		return true;
	}
	
	/**
	 * Gets the list of installed tasks
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getInstalledTasks($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core', 
									'name'   => 'Task',
								))
								->setDbConnection($conn)
								->find($criteria);
	}
	
	/**
	 * Gets task's options
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @param array $defaultOptions The default options
	 * @return array
	 */
	public static function getOptions($name, $module, $defaultOptions = array())
	{
		$conn	 = Core_Services_Db::getConnection();
		$options = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Task',
									))
									->setDbConnection($conn)
									->getOptions($name, $module);
		if ($options == null || empty($options)) {
			return $defaultOptions;
		}
		return Zend_Json::decode($options);
	}
	
	/**
	 * Gets task's options
	 * 
	 * @param Core_Base_Extension_Task $task The task instance
	 * @param array $defaultOptions The default options
	 * @return array
	 */
	public static function getOptionsByInstance($task, $defaultOptions = array())
	{
		if (!($task instanceof Core_Base_Extension_Task)) {
			throw new Exception('The param is not an instance of Core_Base_Extension_Task');
		}
		return self::getOptions($task->getName(), $task->getModule(), $defaultOptions);
	}	
	
	/**
	 * Gets task instance by given task's name and module's name
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @return Core_Models_Task|null
	 */
	public static function getTaskInstance($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'tasks' . DS . $name . DS . 'about.php';
		if (!file_exists($file)) {
			return null;
		}
		$info = include $file;
		if (!is_array($info)) {
			return null;
		}
		return new Core_Models_Task(array(
			'module'	  => $module,
			'name'		  => $name,
			'title'		  => $info['title']['description'],
			'description' => $info['description']['description'],
			'thumbnail'	  => $info['thumbnail'],
			'website'	  => $info['website'],
			'author'	  => $info['author'],
			'email'		  => $info['email'],
			'version'	  => $info['version'],
			'app_version' => $info['appVersion'],
			'license'	  => $info['license'],
			'time_mask'	  => $info['timeMask'],
			'options'	  => $info['options'],
			'actions'	  => (isset($info['actions']) && is_array($info['actions'])) ? $info['actions'] : null, 
		));
	}
	
	/**
	 * Installs a cron task
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @return bool
	 */
	public static function install($name, $module)
	{
		$name	= strtolower($name);
		$module	= strtolower($module);	
		$conn	= Core_Services_Db::getConnection();
		$taskId = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Task',
								   ))
								   ->setDbConnection($conn)
								   ->install($name, $module);
		if ($taskId === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Tasks_' . ucfirst($name) . '_Task';
		if (class_exists($class)) {
			$task = new $class();
			if ($task instanceof Core_Base_Extension_Task) {
				$task->install();
			}
		}
		
		// Execute install callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'tasks' . DS . $name . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['install']['callbacks'])) {
				foreach ($info['install']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		// Execute hook
		Core_Base_Hook_Registry::getInstance()->executeAction('Core_Services_Task_Install_Success_' . $module . $name);
		
		return true;
	}
	
	/**
	 * Updates task's options
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of task
	 * @param array $options
	 * @return bool
	 */
	public static function setOptions($name, $module, $options)
	{
		if (!$name || !$module) {
			return false;
		}
		$options = ($options == null || !is_array($options)) ? null : Zend_Json::encode($options);  
		$conn	 = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core', 
							'name'   => 'Task',
						 ))
						 ->setDbConnection($conn)
						 ->setOptions($name, $module, $options);
		return true;
	}
	
	/**
	 * Sets options to given instance of task
	 * 
	 * @param Core_Base_Extension_Task $task The hook instance
	 * @param array $options
	 * @return bool
	 */
	public static function setOptionsForInstance($task, $options)
	{
		if (!($task instanceof Core_Base_Extension_Task)) {
			throw new Exception('The param is not an instance of Core_Base_Extension_Task');
		}
		return self::setOptions($task->getName(), $task->getModule(), $options);
	}	
	
	/**
	 * Uninstalls a cron task
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @return bool
	 */
	public static function uninstall($name, $module)
	{
		$name	= strtolower($name);
		$module	= strtolower($module);	
		$conn	= Core_Services_Db::getConnection();
		$result = Core_Services_Dao::factory(array(
										'module' => 'core', 
										'name'   => 'Task',
								   ))
								   ->setDbConnection($conn)
								   ->uninstall($name, $module);
		if ($result === false) {
			return false;
		}
		
		$class = ucfirst($module) . '_Tasks_' . ucfirst($name) . '_Task';
		if (class_exists($class)) {
			$task = new $class();
			if ($task instanceof Core_Base_Extension_Task) {
				$task->uninstall();
			}
		}
		
		// Execute uninstall callbacks
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'tasks' . DS . $name . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
			if (isset($info['uninstall']['callbacks'])) {
				foreach ($info['uninstall']['callbacks'] as $callback) {
					call_user_func($callback);
				}
			}
		}
		
		// Execute hook
		Core_Base_Hook_Registry::getInstance()->executeAction('Core_Services_Task_Uninstall_Success_' . $module . $name);
		
		return true;
	}
	
	/**
	 * Updates the last and next execution times
	 *  
	 * @param Core_Models_Task $task The task instance
	 * @return void
	 */
	public static function updateExecutionTimes($task)
	{
		if ($task == null || !($task instanceof Core_Models_Task)) {
			throw new Exception('The param is not an instance of Core_Models_Task');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Task',
						 ))
						 ->setDbConnection($conn)
						 ->updateExecutionTimes($task);
	}
	
	/**
	 * Updates the time mask
	 * 
	 * @param Core_Models_Task $task The task instance
	 * @return void
	 */
	public static function updateTimeMask($task)
	{
		if ($task == null || !($task instanceof Core_Models_Task)) {
			throw new Exception('The param is not an instance of Core_Models_Task');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'Task',
						 ))
						 ->setDbConnection($conn)
						 ->updateTimeMask($task);
	}
}
