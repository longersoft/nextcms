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

class Message_Services_Attachment
{
	/**
	 * Deletes attachment
	 * 
	 * @param string $path The file path in the format of ###user_id###/###year###/###month###/###file###
	 * @param Core_Models_User $user The user instance
	 * @return bool
	 */
	public static function delete($path, $user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		
		$path = ltrim($path, '/');
		$path = rtrim($path, '/');
		if (!preg_match('/^' . $user->user_id . '\/(\d{4})\/(\d{2})\/(.+)$/', $path)) {
			// Invalid path
			return false;
		}
		
		$prefixPath = Core_Services_Config::get('message', 'attachments_dir', Message_Services_Installer::DEFAULT_ATTACHMENTS_DIR);
		$prefixPath = rtrim($prefixPath, '/');
		$prefixPath = ltrim($prefixPath, '/');
		
		$file = APP_ROOT_DIR . '/' . $prefixPath . '/' . $path;
		if (!file_exists($file)) {
			return false;
		}
		return @unlink($file);
	}
	
	/**
	 * Downloads the attachment
	 * 
	 * @param string $path The attachment path
	 * @param Core_Models_User $user The user instance
	 * @return string The full path of attachment
	 */
	public static function download($path, $user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		
		$path = ltrim($path, '/');
		$path = rtrim($path, '/');
		if (!preg_match('/^' . $user->user_id . '\/(\d{4})\/(\d{2})\/(.+)$/', $path)) {
			throw new Exception('Invalid path');
		}
		
		// Only allow users who receive the private message to download the attachment
		$conn		 = Core_Services_Db::getConnection();
		$canDownload = Core_Services_Dao::factory(array(
											'module' => 'message',
											'name'   => 'Message',
										))
										->setDbConnection($conn)
										->canDownloadAttachment($path, $user);
		if (!$canDownload) {
			throw new Exception('User [user_id=' . $user->user_id . '] cannot download the attachment [path=' . $path . ']');
		}
										
		$prefixPath = Core_Services_Config::get('message', 'attachments_dir', Message_Services_Installer::DEFAULT_ATTACHMENTS_DIR);
		$prefixPath = rtrim($prefixPath, '/');
		$prefixPath = ltrim($prefixPath, '/');
		
		return APP_ROOT_DIR . '/' . $prefixPath . '/' . $path;
	}
	
	/**
	 * Uploads attachments
	 * 
	 * @param string $fileName
	 * @param Core_Models_User $user The user instance
	 * @return array An array of uploaded files, which each item consists of
	 * the following members:
	 * - path
	 * - name
	 * - extension
	 * - size
	 */
	public static function upload($fileName, $user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The second param is not an instance of Core_Models_User');
		}
		
		// The attachments will be stored in the directory of 
		// APP_ROOT_DIR . '/upload/message/__attachments/###user_id###/###year###/###month###/###file###
		$prefixPath = Core_Services_Config::get('message', 'attachments_dir', Message_Services_Installer::DEFAULT_ATTACHMENTS_DIR);
		$prefixPath = rtrim($prefixPath, '/');
		$prefixPath = ltrim($prefixPath, '/');
		
		$userDir	= $user->user_id . '/' . date('Y') . '/' . date('m');
		$uploadDir  = $prefixPath . '/' . $userDir;
		Core_Base_File::createDirectories($uploadDir, APP_ROOT_DIR);
		
		// Upload files
		$files	  = array();
		$numFiles = count($_FILES[$fileName]['name']);
		
		$allowedExtensions = Core_Services_Config::get('message', 'attachments_exts', '');
		$allowedExtensions = ($allowedExtensions == '') ? array() : explode(',', $allowedExtensions);
		
		for ($i = 0; $i < $numFiles; $i++) {
			$extension		= explode('.', $_FILES[$fileName]['name'][$i]);
			$extension		= strtolower($extension[count($extension) - 1]);
			
			if (count($allowedExtensions) > 0 && !in_array($extension, $allowedExtensions)) {
				continue;
			}
			
			$name			= basename($_FILES[$fileName]['name'][$i], '.' . $extension);
			$fileId			= uniqid();
			$uploadFileName = $fileId . '.' . $extension;
			$uploadFilePath = APP_ROOT_DIR . DS . $uploadDir . DS . $uploadFileName;
			
			// Move uploaded file to the target directory
			move_uploaded_file($_FILES[$fileName]['tmp_name'][$i], $uploadFilePath);
			
			$files[] = array(
				'path'		=> $userDir . '/' . $uploadFileName,
				'name'		=> $name,
				'extension' => $extension,
				'size'		=> filesize($uploadFilePath),
			);
		}
		
		return $files;
	}
}
