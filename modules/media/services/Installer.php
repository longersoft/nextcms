<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	services
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Services_Installer
{
	/**
	 * The callback that is called after installing the module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		Core_Services_Hook::install('editor', 'media');
		Core_Services_Hook::install('photoprovider', 'media');
		Core_Services_Hook::install('videoprovider', 'media');
		
		Core_Services_Widget::install('albums', 'media');
		Core_Services_Widget::install('photos', 'media');
		Core_Services_Widget::install('playlists', 'media');
		Core_Services_Widget::install('searchbox', 'media');
		Core_Services_Widget::install('videos', 'media');
	}
	
	/**
	 * The callback that is called after uninstalling the module
	 * 
	 * @return void
	 */
	public static function uninstallModule()
	{
		Core_Services_Hook::uninstall('editor', 'media');
		Core_Services_Hook::uninstall('photoprovider', 'media');
		Core_Services_Hook::uninstall('videoprovider', 'media');
		
		Core_Services_Widget::uninstall('albums', 'media');
		Core_Services_Widget::uninstall('photos', 'media');
		Core_Services_Widget::uninstall('playlists', 'media');
		Core_Services_Widget::uninstall('searchbox', 'media');
		Core_Services_Widget::uninstall('videos', 'media');
	}
}
