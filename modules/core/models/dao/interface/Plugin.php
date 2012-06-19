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

interface Core_Models_Dao_Interface_Plugin
{
	/**
	 * Enables/disables a plugin
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @param bool $enable If TRUE, enables the plugin. If FALSE, disable the plugin
	 * @return void
	 */
	public function enable($name, $module, $enable = true);	
	
	/**
	 * Gets plugin's options
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @return string
	 */
	public function getOptions($name, $module);
	
	/**
	 * Gets list of plugins
	 * 
	 * @param bool $enabled If TRUE, returns the enabled plugins
	 * @return Core_Base_Models_RecordSet
	 */
	public function getPlugins($enabled = null);
	
	/**
	 * Installs new plugin
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @return string Id of newly added plugin
	 */
	public function install($name, $module);

	/**
	 * Uninstalls plugin
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @return bool
	 */
	public function uninstall($name, $module);
	
	/**
	 * Updates plugin's options
	 * 
	 * @param string $name Name of plugin
	 * @param string $module Name of module
	 * @param string $options
	 * @return void
	 */
	public function setOptions($name, $module, $options);
}
