<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	services
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Services_Installer
{
	/**
	 * The default attachments directory
	 * 
	 * @var string
	 */
	const DEFAULT_ATTACHMENTS_DIR = '/upload/message/__attachments';
	
	/**
	 * Called after installing the Message module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		// Create a directory to store private message attachments
		Core_Base_File::createDirectories(self::DEFAULT_ATTACHMENTS_DIR, APP_ROOT_DIR);
		@file_put_contents(APP_ROOT_DIR . self::DEFAULT_ATTACHMENTS_DIR . DS . '.htaccess', 'deny from all');
		
		Core_Services_Config::set('message', 'attachments_dir', self::DEFAULT_ATTACHMENTS_DIR);
	}
	
	/**
	 * Called after uninstalling the Message module
	 * 
	 * @return void
	 */
	public static function uninstallModule()
	{
	}
}
