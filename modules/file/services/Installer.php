<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	services
 * @since		1.0
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Services_Installer
{
	/**
	 * The template name of upload directory. It can consist of the following strings:
	 * - {#module#} (when creating the directory to store the uploaded file, it will be replaced with the module's name)
	 * - {#user_id#} (replaced with the Id of current user)
	 * - {#user_name#} (replaced with the user name of current user)
	 * - {#year#} (replaced with current year, in 4 digits)
	 * - {#month#} (replaced with current month)
	 * The value of this constant is stored in the config table under the key "upload_dir_template".
	 * 
	 * @var const
	 */
	const DEFAULT_UPLOAD_DIR_TEMPLATE = '/upload/{#module#}/{#user_id#}/{#year#}/{#month#}';	
	
	/**
	 * The information of various thumbnails if the uploaded file is an image.
	 * It is stored in the config table under the key "image_thumbnails" in the JSON format. Each property looks like:
	 * "nameOfThumbnail": "methodToGenerate|width|height"
	 * 
	 * @var const
	 */
	const DEFAULT_IMAGE_THUMBNAILS = '{"square": "crop|75|75", "thumbnail": "resize|100|100", "small": "resize|240|240", "crop": "crop|300|225", "medium": "resize|640|640", "large": "resize|1024|1024"}';

	/**
	 * The toolkit for processing images.
	 * It is stored in the config table under the key "image_toolkit".
	 * 
	 * @var const
	 */
	const DEFAULT_IMAGE_TOOLKIT = 'gd';
	
	/**
	 * The default font of watermark 
	 * 
	 * @var const
	 */
	const DEFAULT_WATERMARK_FONT = '/modules/file/data/watermark.ttf';
	
	/**
	 * The default attachments directory
	 * 
	 * @var string
	 */
	const DEFAULT_ATTACHMENTS_DIR = '/upload/file/__attachments';
	
	/**
	 * The callback that is called after installing the module
	 * 
	 * @return void
	 */
	public static function installModule()
	{
		Core_Services_Hook::install('attachmentprovider', 'file');
		Core_Services_Hook::install('explorer', 'file');
		Core_Services_Hook::install('uploader', 'file');
		
		Core_Services_Widget::install('attachments', 'file');
		
		// Init basic configurations
		Core_Services_Config::set('file', 'image_thumbnails', self::DEFAULT_IMAGE_THUMBNAILS);
		Core_Services_Config::set('file', 'upload_dir_template', self::DEFAULT_UPLOAD_DIR_TEMPLATE);
		Core_Services_Config::set('file', 'image_toolkit', self::DEFAULT_IMAGE_TOOLKIT);
		
		// Create a directory to store private message attachments
		Core_Base_File::createDirectories(self::DEFAULT_ATTACHMENTS_DIR, APP_ROOT_DIR);
		@file_put_contents(APP_ROOT_DIR . self::DEFAULT_ATTACHMENTS_DIR . DS . '.htaccess', 'deny from all');
	}
	
	/**
	 * The callback that is called after uninstalling the module
	 * 
	 * @return void
	 */
	public static function uninstallModule()
	{
		Core_Services_Hook::uninstall('attachmentprovider', 'file');
		Core_Services_Hook::uninstall('explorer', 'file');
		Core_Services_Hook::uninstall('uploader', 'file');
		
		Core_Services_Widget::uninstall('attachments', 'file');
	}
}
