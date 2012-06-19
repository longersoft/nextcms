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
 * @version		2011-10-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_Hook
{
	/**
	 * Gets list of hooks
	 * 
	 * @param array $criteria
	 * @return Core_Base_Models_RecordSet
	 */
	public function getHooks($criteria = array());
	
	/**
	 * Gets hook's options
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @return string
	 */
	public function getOptions($name, $module);
	
	/**
	 * Installs new hook
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @return string Id of newly added hook
	 */
	public function install($name, $module);

	/**
	 * Uninstalls new hook
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @return bool
	 */
	public function uninstall($name, $module);
	
	/**
	 * Updates hook's options
	 * 
	 * @param string $name Name of hook
	 * @param string $module Name of module
	 * @param string $options
	 * @return void
	 */
	public function setOptions($name, $module, $options);
}
