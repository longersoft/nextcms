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

interface Core_Models_Dao_Interface_Widget
{
	/**
	 * Finds installed widgets
	 * 
	 * @param array $criteria Contains the following keys:
	 * - module: The name of module that widgets belong to
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array());
	
	/**
	 * Installs widget
	 * 
	 * @param string $name Name of widget
	 * @param string $module Name of module
	 * @return string Id of newly added widget
	 */
	public function install($name, $module);

	/**
	 * Uninstalls widget
	 * 
	 * @param string $name Name of widget
	 * @param string $module Name of module
	 * @return bool
	 */
	public function uninstall($name, $module);
}
