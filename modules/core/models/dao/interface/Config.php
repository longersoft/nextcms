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

interface Core_Models_Dao_Interface_Config
{
	/**
	 * Deletes given setting
	 * 
	 * @param string $module
	 * @param string $key
	 * @return void
	 */
	public function delete($module, $key);
	
	/**
	 * Gets config value
	 * 
	 * @param string $module Module name
	 * @param string $key Key name
	 * @return string
	 */
	public function get($module, $key);
	
	/**
	 * Replaces setting. It will update the setting if the setting is found, otherwise it create a new one
	 * 
	 * @param string $module
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function set($module, $key, $value);
}
