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
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_Config
{
	/**
	 * Gets the value of given setting
	 * 
	 * @param string $module
	 * @param string $key
	 * @param string $defaultValue
	 * @return string
	 */
	public function config($module, $key, $default = null)
	{
		Core_Services_Db::connect('slave');
		return Core_Services_Config::get($module, $key, $default);
	}
}
