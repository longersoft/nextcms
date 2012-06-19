<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		util
 * @subpackage	services
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Services_Installer
{
	/**
	 * The callback that is called after installing the module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		Core_Services_Hook::install('slideshow', 'util');
		
		Core_Services_Widget::install('feed', 'util');
		Core_Services_Widget::install('googlemap', 'util');
		Core_Services_Widget::install('share', 'util');
		Core_Services_Widget::install('social', 'util');
		Core_Services_Widget::install('twitter', 'util');
		Core_Services_Widget::install('urlshortener', 'util');
	}
	
	/**
	 * The callback that is called after uninstalling the module
	 * 
	 * @return void
	 */
	public static function uninstallModule()
	{
		Core_Services_Hook::uninstall('slideshow', 'util');
		
		Core_Services_Widget::uninstall('feed', 'util');
		Core_Services_Widget::uninstall('googlemap', 'util');
		Core_Services_Widget::uninstall('share', 'util');
		Core_Services_Widget::uninstall('social', 'util');
		Core_Services_Widget::uninstall('twitter', 'util');
		Core_Services_Widget::uninstall('urlshortener', 'util');
	}
}
