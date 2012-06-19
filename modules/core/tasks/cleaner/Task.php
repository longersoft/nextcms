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

class Core_Tasks_Cleaner_Task extends Core_Base_Extension_Task
{
	/**
	 * @see Core_Base_Extension_Task::execute()
	 */
	public function execute($params = null)
	{
		Core_Services_Db::connect('master');
		$this->_deleteCaptcha();
		$this->_deleteLogs();
	}
	
	/**
	 * Deletes captcha images
	 * 
	 * @return void
	 */
	private function _deleteCaptcha()
	{
		$captchaDir = APP_TEMP_DIR . DS . 'captcha';
		if (!file_exists($captchaDir)) {
			return;
		}
		$dirIterator = new DirectoryIterator($captchaDir);
		foreach ($dirIterator as $dir) {
			if ($dir->isDot() || $dir->isDir()) {
				continue;
			}
			$file = $captchaDir . DS . $dir->getFilename();
			// Delete file which was generated more than 10 minutes ago
			if (time() - filemtime($file) > 600) {
				@unlink($file);
			}
		}
	}
	
	/**
	 * Deletes logs
	 *
	 * @return void
	 */
	private function _deleteLogs()
	{
		$options	  = Core_Services_Task::getOptionsByInstance($this, array(
			'accesslog_days' => 1,
			'error_days'	 => 1,
		));
		$conn		  = Core_Services_Db::getConnection();
		$accessLogDao = Core_Services_Dao::factory(array(
											'module' => 'core',
											'task'   => 'cleaner',
											'name'   => 'AccessLog',
									     ))
									     ->setDbConnection($conn);
		$errorDao	  = Core_Services_Dao::factory(array(
											'module' => 'core',
											'task'   => 'cleaner',
											'name'   => 'Error',
									     ))
									     ->setDbConnection($conn);
		if (isset($options['accesslog_days']) && (int) $options['accesslog_days'] > 0) {
			$accessLogDao->deleteAccessLogs((int) $options['accesslog_days']);
		}
		if (isset($options['error_days']) && (int) $options['error_days'] > 0) {
			$errorDao->deleteErrors((int) $options['error_days']);
		}
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$options = Core_Services_Task::getOptionsByInstance($this);
		$this->view->assign(array(
			'accessLogDays' => $options ? $options['accesslog_days'] : -1,
			'errorDays'		=> $options ? $options['error_days'] : -1,
		));
	}
	
	/**
	 * Saves the settings
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$options = array(
			'accesslog_days' => $request->getParam('accesslog_days', -1),
			'error_days'	 => $request->getParam('error_days', -1),
		);
		$result = Core_Services_Task::setOptionsForInstance($this, $options);
		return $result ? 'true' : 'false';
	}
}
