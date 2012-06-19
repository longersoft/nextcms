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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_Task
{
	/**
	 * Searches for installed tasks
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array());
	
	/**
	 * Gets task's options
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @return string
	 */
	public function getOptions($name, $module);
	
	/**
	 * Installs new cron task
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @return string Id of newly added task
	 */
	public function install($name, $module);
	
	/**
	 * Uninstalls a cron task
	 * 
	 * @param string $name Name of task
	 * @param string $module Name of module
	 * @return bool
	 */
	public function uninstall($name, $module);
	
	/**
	 * Updates the last and next execution times
	 *  
	 * @param Core_Models_Task $task The task instance
	 * @return void
	 */
	public function updateExecutionTimes($task);
	
	/**
	 * Updates task's options
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of task
	 * @param array $options
	 * @return void
	 */
	public function setOptions($name, $module, $options);
	
	/**
	 * Updates the time mask
	 * 
	 * @param Core_Models_Task $task The task instance
	 * @return void
	 */
	public function updateTimeMask($task);
}
