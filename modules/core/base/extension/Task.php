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
 * @subpackage	base
 * @since		1.0
 * @version		2012-03-03
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a task which can be scheduled
 */
abstract class Core_Base_Extension_Task extends Core_Base_Extension
{
	public function __construct($name = null, $module = null)
	{
		if ($name == null || $module == null) {
			$class	= get_class($this);
			$paths	= explode('_', $class);
			$name	= strtolower($paths[2]);
			$module = strtolower($paths[0]);
		}
		
		parent::__construct($name, $module);
		
		$path = 'modules' . DS . $module . DS . 'tasks' . DS . $name;
		
		$this->addHelperPath(APP_ROOT_DIR . DS . $path, $module . '_Tasks_' . $name . '_')
			 ->addScriptPath(APP_ROOT_DIR . DS . $path)
			 ->setLanguagePath($path);
	}
	
	/**
	 * Execute the task
	 * 
	 * @param mixed $params
	 * @return void
	 */
	abstract public function execute($params = null);
}
