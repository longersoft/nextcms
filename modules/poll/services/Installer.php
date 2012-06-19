<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	services
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_Services_Installer
{
	/**
	 * The callback that is called after installing the module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		Core_Services_Widget::install('poll', 'poll');
	}
	
	/**
	 * The callback that is called after uninstalling the module
	 * 
	 * @return void
	 */
	public static function uninstallModule()
	{
		Core_Services_Widget::uninstall('poll', 'poll');
	}
}
