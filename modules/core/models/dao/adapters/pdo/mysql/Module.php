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

class Core_Models_Dao_Adapters_Pdo_Mysql_Module extends Core_Base_Models_Dao 
	implements Core_Models_Dao_Interface_Module
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Module($entity); 
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Module::getModules()
	 */
	public function getModules()
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_module')
					   ->order('name ASC')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Module::install()
	 */
	public function install($module)
	{
		// Get the module information file
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'configs' . DS . 'about.php';
		if (!file_exists($file)) {
			return null;
		}
		
		$info = include $file;
		
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
		$this->_removePrivileges($module);
		
		// Add resources, privileges
		$privileges = Core_Services_Privilege::getExtensionPrivileges($module);
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
		
		// Add new module
		$this->_conn->insert($this->_prefix . 'core_module',
							array(
								'name' 			   => $info['name'],
								'title' 		   => $info['title']['description'],
								'description' 	   => $info['description']['description'],
								'thumbnail' 	   => $info['thumbnail'],
								'website' 		   => $info['website'],
								'author' 		   => $info['author'],
								'email' 		   => $info['email'],
								'version' 		   => $info['version'],
								'app_version' 	   => $info['appVersion'],
								'required_modules' => $info['requirements']['modules'],
								'license' 		   => $info['license'],
							));
		$moduleId = $this->_conn->lastInsertId($this->_prefix . 'core_module');
		
		// Return the module instance
		return new Core_Models_Module(array(
			'module_id'		   => $moduleId,
			'name' 			   => $info['name'],
			'title' 		   => $info['title']['description'],
			'description' 	   => $info['description']['description'],
			'thumbnail' 	   => $info['thumbnail'],
			'website' 		   => $info['website'],
			'author' 		   => $info['author'],
			'email' 		   => $info['email'],
			'version' 		   => $info['version'],
			'app_version' 	   => $info['appVersion'],
			'required_modules' => $info['requirements']['modules'],
			'license' 		   => $info['license'],
		));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Module::uninstall()
	 */
	public function uninstall($module)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'configs' . DS . 'about.php';
		if (file_exists($file)) {
			$info = include $file;
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
		}
		
		// Remove associating privileges
		$this->_removePrivileges($module);
		
		// Remove the module from module table
		$this->_conn->delete($this->_prefix . 'core_module', 
							array(
								'name = ?' => $module,
							));
	}
	
	/**
	 * Removes all resources, privileges associating with the module
	 * 
	 * @param string $module The module's name
	 * @return void
	 */
	private function _removePrivileges($module)
	{
		$criteria = array(
			'module_name = ?' => $module,
		);
		$this->_conn->delete($this->_prefix . 'core_resource', $criteria);
		$this->_conn->delete($this->_prefix . 'core_privilege', $criteria);
		$this->_conn->delete($this->_prefix . 'core_rule', $criteria);
	}
}
