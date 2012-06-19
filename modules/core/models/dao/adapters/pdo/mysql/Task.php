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
 * @subpackage	models
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_Task extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Task
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Task($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::find()
	 */
	public function find($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_task');
		if (isset($criteria['module']) && !empty($criteria['module'])) {
			$select->where('module = ?', $criteria['module']);
		}
		if (isset($criteria['name']) && !empty($criteria['name'])) {
			$select->where('name = ?', $criteria['name']);
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'name';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'ASC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::getOptions()
	 */
	public function getOptions($name, $module)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_task', array('options'))
					->where('name = ?', $name)
					->where('module = ?', $module)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : $row->options;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::install()
	 */
	public function install($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'tasks' . DS . $name . DS . 'about.php';
		if (!file_exists($file)) {
			return false;
		}
		$info = include $file;
		if (!is_array($info)) {
			return false;
		}
		
		// Execute the install queries
		if (isset($info['install']['queries'])) {
			$queries = null;
			foreach ($info['install']['queries'] as $adapters => $value) {
				if (in_array('pdo_mysql', explode(',', $adapters))) {
					$queries = $value;
					break;
				}
			}
			if ($queries) {
				foreach ($queries as $query) {
					try {
						$this->_conn->beginTransaction();
						$query = str_replace('###', $this->_prefix, $query);
						$this->_conn->query($query);
						$this->_conn->commit();
					} catch (Exception $ex) {
						$this->_conn->rollBack();
						break;
					}
				}
			}
		}
		
		// Remove associating privileges
		$this->_removePrivileges($name, $module);
		
		// Add resources, privileges
		$privileges = Core_Services_Privilege::getExtensionPrivileges($module, $name, 'task');
		if ($privileges) {
			foreach ($privileges as $priv) {
				$this->_conn->insert($this->_prefix . 'core_resource', 
									array(
										'parent_id' 	  => $priv['resource']->parent_id,
										'description' 	  => $priv['resource']->description,
										'module_name' 	  => $priv['resource']->module_name,
										'controller_name' => $priv['resource']->controller_name,
										'extension_type'  => $priv['resource']->extension_type,
									));
				foreach ($priv['privileges'] as $privilege) {
					$this->_conn->insert($this->_prefix . 'core_privilege', 
										array(
											'description' 	  => $privilege->description,
											'module_name' 	  => $privilege->module_name,
											'controller_name' => $privilege->controller_name,
											'action_name'     => $privilege->action_name,
											'extension_type'  => $privilege->extension_type,
										));
				}
			}
		}
		
		$timeMask = isset($info['timeMask']) ? $info['timeMask'] : '* * * * *';
		$options  = (isset($info['options']) && is_array($info['options']))
					? Zend_Json::encode($info['options'])
					: null;
		
		// Add new task
		$this->_conn->insert($this->_prefix . 'core_task',
							array(
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
								'time_mask'   => $timeMask,
								'options'	  => $options,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_task');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::setOptions()
	 */
	public function setOptions($name, $module, $options)
	{
		$this->_conn->update($this->_prefix . 'core_task',
							array(
								'options' => $options,
							),
							array(
								'name = ?'	 => $name,
								'module = ?' => $module,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::uninstall()
	 */
	public function uninstall($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'tasks' . DS . $name . DS . 'about.php';
		if (!file_exists($file)) {
			return false;
		}
		$info = include $file;
		if (!is_array($info)) {
			return false;
		}
		
		// Execute the uninstall queries
		if (isset($info['uninstall']['queries'])) {
			$queries = null;
			foreach ($info['uninstall']['queries'] as $adapters => $value) {
				if (in_array('pdo_mysql', explode(',', $adapters))) {
					$queries = $value;
					break;
				}
			}
			if ($queries) {
				foreach ($queries as $query) {
					try {
						$this->_conn->beginTransaction();
						$query = str_replace('###', $this->_prefix, $query);
						$this->_conn->query($query);
						$this->_conn->commit();
					} catch (Exception $ex) {
						$this->_conn->rollBack();
						break;
					}
				}
			}
		}
		
		// Remove associating privileges
		$this->_removePrivileges($name, $module);
		
		// Remove the task from DB
		$this->_conn->delete($this->_prefix . 'core_task', 
							array(
								'name = ?'	 => $name,
								'module = ?' => $module,
							));
		return true;
	}
	
	/**
	 * Removes all resources, privileges associating with the task
	 * 
	 * @param string $name The task's name
	 * @param string $module The module's name
	 * @return void
	 */
	private function _removePrivileges($name, $module)
	{
		$criteria = array(
			'module_name = ?'	  => $module,
			'controller_name = ?' => $name,
			'extension_type = ?'  => 'task',
		);
		$this->_conn->delete($this->_prefix . 'core_resource', $criteria);
		$this->_conn->delete($this->_prefix . 'core_privilege', $criteria);
		$this->_conn->delete($this->_prefix . 'core_rule', $criteria);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::updateExecutionTimes()
	 */
	public function updateExecutionTimes($task)
	{
		$this->_conn->update($this->_prefix . 'core_task',
							array(
								'last_run' => $task->last_run,
								'next_run' => $task->next_run,
							),
							array(
								'name = ?'	 => $task->name,
								'module = ?' => $task->module,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Task::updateTimeMask()
	 */
	public function updateTimeMask($task)
	{
		$this->_conn->update($this->_prefix . 'core_task',
							array(
								'time_mask' => $task->time_mask,
							),
							array(
								'name = ?'	 => $task->name,
								'module = ?' => $task->module,
							));
	}
}
