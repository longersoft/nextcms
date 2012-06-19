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

interface Core_Models_Dao_Interface_Module
{
	/**
	 * Gets the list of installed modules
	 * 
	 * @return Core_Base_Models_RecordSet
	 */
	public function getModules();
	
	/**
	 * Installs module
	 * 
	 * @param string $module The module's name
	 * @return Core_Models_Module|null
	 */
	public function install($module);
	
	/**
	 * Uninstalls module
	 * 
	 * @param string $module The module's name
	 * @return bool
	 */
	public function uninstall($module);
}
