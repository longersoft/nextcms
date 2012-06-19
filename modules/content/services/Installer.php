<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	services
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Services_Installer
{
	/**
	 * The callback that is called after installing the module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		Core_Services_Hook::install('linkprovider', 'content');
		Core_Services_Task::install('publisher', 'content');
		Core_Services_Widget::install('archive', 'content');
		Core_Services_Widget::install('articles', 'content');
		Core_Services_Widget::install('breadcrumb', 'content');
		Core_Services_Widget::install('categories', 'content');
		Core_Services_Widget::install('editor', 'content');
		Core_Services_Widget::install('searchbox', 'content');
		Content_Services_Sitemap::addDeclaration();
	}
	
	/**
	 * The callback that is called after uninstalling the module
	 * 
	 * @return void
	 */
	public static function uninstallModule()
	{
		Core_Services_Hook::uninstall('linkprovider', 'content');
		Core_Services_Task::uninstall('publisher', 'content');
		Core_Services_Widget::uninstall('archive', 'content');
		Core_Services_Widget::uninstall('articles', 'content');
		Core_Services_Widget::uninstall('breadcrumb', 'content');
		Core_Services_Widget::uninstall('categories', 'content');
		Core_Services_Widget::uninstall('editor', 'content');
		Core_Services_Widget::uninstall('searchbox', 'content');
		Content_Services_Sitemap::removeDeclaration();
	}
}
