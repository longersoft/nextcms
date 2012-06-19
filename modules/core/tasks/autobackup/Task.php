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
 * @subpackage	tasks
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Tasks_Autobackup_Task extends Core_Base_Extension_Task
{
	/**
	 * @see Core_Base_Extension_Task::execute()
	 */
	public function execute($params = null)
	{
		// Exit if the app does not use MySql database
		$config	  = Core_Services_Config::getAppConfigs();
		$adapater = $config['db']['adapter'];
		if (!in_array($adapater, array('mysql', 'pdo_mysql'))) {
			return;
		}
		
		$this->_createDir();
		
		// Get array of tables with name and the number of rows
		$conn	= Core_Services_Db::connect('master');
		$dao    = Core_Services_Dao::factory(array(
										'module' => 'core',
										'task'   => 'autobackup',
										'name'   => 'Dumper',
								   ))
								   ->setDbConnection($conn);
		$queries = $dao->getQueries();
		if (count($queries) == 0) {
			return;
		}
		
		// Dump the data in each table to SQL file
		$sqlFile = APP_ROOT_DIR . DS . 'data' . DS . 'backup' . DS . date('Y_m_d-H_i') . '-' . md5(uniqid()) . '.sql';
		foreach ($queries as $query) {
			file_put_contents($sqlFile, $query . "\n", FILE_APPEND);
		}
	}
	
	/**
	 * @see Core_Base_Extension::install()
	 */
	public function install()
	{
		$this->_createDir();
	}
	
	/**
	 * Deletes SQL file
	 * 
	 * @return array
	 */
	public function deleteAction()
	{
		$name = $this->getRequest()->getParam('name');
		$file = APP_ROOT_DIR . DS . 'data' . DS . 'backup' . DS . $name;
		if (file_exists($file)) {
			@unlink($file);
		}
		return array(
			'result' => 'APP_RESULT_OK',
		);
	}
	
	/**
	 * Downloads SQL file
	 * 
	 * @return void
	 */
	public function downloadAction()
	{
		$name = $this->getRequest()->getParam('name');
		$file = APP_ROOT_DIR . DS . 'data' . DS . 'backup' . DS . $name;
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
		}
		exit();
	}
	
	/**
	 * Views SQL files
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		$files	   = array();
		$backupDir = APP_ROOT_DIR . DS . 'data' . DS . 'backup';
		if (file_exists($backupDir)) {
			$dirIterator = new DirectoryIterator($backupDir);
			foreach ($dirIterator as $dir) {
				if ($dir->isDot() || $dir->isDir()) {
					continue;
				}
				$fileName = $dir->getFilename();
				$files[]  = array(
					'name'			=> $fileName,
					'last_modified' => date('Y-m-d H:i:s', filemtime($backupDir . DS. $fileName)),
				);
			}
		}
		
		$request = $this->getRequest();
		$format  = $request->getParam('_format');
		switch ($format) {
			// Build the data for the file grid
			case 'json':
				return array(
					'identifier' => 'name',
					'items'		 => $files,
				);
				break;
			default:
				$this->view->assign(array(
					'files'	   => $files,
					'uniqueId' => uniqid(),
				));
				break;
		}
	}
	
	/**
	 * Creates the backup directory
	 * 
	 * @return void
	 */
	private function _createDir()
	{
		$backupDir = APP_ROOT_DIR . DS . 'data' . DS . 'backup';
		if (!file_exists($backupDir)) {
			@mkdir($backupDir);
		}
	}
}
