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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_Plugin extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Plugin
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Plugin($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Plugin::enable()
	 */
	public function enable($name, $module, $enable = true)
	{
		$this->_conn->update($this->_prefix . 'core_plugin',
							array(
								'enabled' => $enable ? 1 : 0,
							),
							array(
								'name = ?'	 => $name,
								'module = ?' => $module,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Plugin::getOptions()
	 */
	public function getOptions($name, $module)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_plugin', array('options'))
					->where('name = ?', $name)
					->where('module = ?', $module)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : $row->options;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Plugin::getPlugins()
	 */
	public function getPlugins($enabled = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_plugin')
					   ->order('ordering ASC');
		if (is_bool($enabled)) {
			$select->where('enabled = ?', $enabled ? 1 : 0);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Plugin::install()
	 */
	public function install($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'plugins' . DS . $name . DS . 'about.php';
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
		$privileges = Core_Services_Privilege::getExtensionPrivileges($module, $name, 'plugin');
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
		
		// Add new plugin
		$this->_conn->insert($this->_prefix . 'core_plugin',
							array(
								'ordering'	  => 0,
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
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_plugin');
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Plugin::uninstall()
	 */
	public function uninstall($name, $module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'plugins' . DS . $name . DS . 'about.php';
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
		
		// Remove the plugin from DB
		$this->_conn->delete($this->_prefix . 'core_plugin', 
							array(
								'name = ?'	 => $name,
								'module = ?' => $module,
							));
		return true;
	}
	
	/**
	 * Removes all resources, privileges associating with the plugin
	 * 
	 * @param string $name The plugin's name
	 * @param string $module The module's name
	 * @return void
	 */
	private function _removePrivileges($name, $module)
	{
		$criteria = array(
			'module_name = ?'	  => $module,
			'controller_name = ?' => $name,
			'extension_type = ?'  => 'plugin',
		);
		$this->_conn->delete($this->_prefix . 'core_resource', $criteria);
		$this->_conn->delete($this->_prefix . 'core_privilege', $criteria);
		$this->_conn->delete($this->_prefix . 'core_rule', $criteria);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Plugin::setOptions()
	 */
	public function setOptions($name, $module, $options)
	{
		$this->_conn->update($this->_prefix . 'core_plugin',
							array(
								'options' => $options,
							),
							array(
								'name = ?'	 => $name,
								'module = ?' => $module,
							));
	}
}
