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

class File_Services_Attachment
{
	/**
	 * Adds new attachment
	 * 
	 * @param File_Models_Attachment $attachment Attachment instance
	 * @return string Id of newly added attachment
	 */
	public static function add($attachment)
	{
		if (!$attachment || !($attachment instanceof File_Models_Attachment)) {
			throw new Exception('The param is not an instance of File_Models_Attachment');
		}
		
		if (!$attachment->file) {
			return false;
		}
		$attachment->sanitize(array('description'));
		
		// Upload attachment
		$fileInfo = self::_uploadFile($attachment->file);
		
		$attachment->name	   = $fileInfo['name'];
		$attachment->hash	   = Core_Base_String::generateHash($fileInfo['name']);
		$attachment->extension = $fileInfo['extension'];
		$attachment->path	   = $fileInfo['path'];
		$attachment->size	   = $fileInfo['size'];
		
		if ($attachment->password) {
			$attachment->password = self::encryptPassword($attachment->password);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Attachment',
								))
								->setDbConnection($conn)
								->add($attachment);
	}
	
	/**
	 * Gets the number of attachments by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Attachment',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return bool
	 */
	public static function delete($attachment)
	{
		if (!$attachment || !($attachment instanceof File_Models_Attachment)) {
			throw new Exception('The param is not an instance of File_Models_Attachment');
		}
		
		// Delete file
		self::_deleteFile($attachment);
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'file',
							'name'   => 'Attachment',
						 ))
						 ->setDbConnection($conn)
						 ->delete($attachment);
		return true;
	}
	
	/**
	 * Checkes if a given password matches with the attachment's password
	 * 
	 * @param string $password The password to check
	 * @param string $compareTo The attachment's password
	 * @return bool
	 */
	public static function checkPassword($password, $compareTo)
	{
		require_once 'PasswordHash.php';
		$hasher = new PasswordHash(8, true);
		return $hasher->CheckPassword($password, $compareTo);
	}
	
	/**
	 * Encrypts the attachment password
	 * 
	 * @param string $password
	 * @return string
	 */
	public static function encryptPassword($password)
	{
		require_once 'PasswordHash.php';
		$hasher = new PasswordHash(8, true);
		return $hasher->HashPassword($password);
	}
	
	/**
	 * Finds attachments by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Attachment',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets attachment instance by given Id
	 * 
	 * @param string $attachmentId Id of attachment
	 * @return File_Models_Attachment|null
	 */
	public static function getById($attachmentId)
	{
		if ($attachmentId == null || empty($attachmentId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'file',
									'name'   => 'Attachment',
								))
								->setDbConnection($conn)
								->getById($attachmentId);
	}
	
	/**
	 * Increases the number of downloads of attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return bool
	 */
	public static function increaseNumDownloads($attachment)
	{
		if (!$attachment || !($attachment instanceof File_Models_Attachment)) {
			throw new Exception('The param is not an instance of File_Models_Attachment');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'file',
							'name'   => 'Attachment',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumDownloads($attachment);
		return true;
	}
	
	/**
	 * Updates given attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return bool
	 */
	public static function update($attachment)
	{
		if (!$attachment || !($attachment instanceof File_Models_Attachment)) {
			throw new Exception('The param is not an instance of File_Models_Attachment');
		}
		if ($attachment->file) {
			// Delete current file
			self::_deleteFile($attachment);
			
			// Upload attachment
			$fileInfo = self::_uploadFile($attachment->file);
			
			$attachment->name	   = $fileInfo['name'];
			$attachment->extension = $fileInfo['extension'];
			$attachment->path	   = $fileInfo['path'];
			$attachment->size	   = $fileInfo['size'];
		}
		$attachment->sanitize(array('description'));
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'file',
							'name'   => 'Attachment',
						 ))
						 ->setDbConnection($conn)
						 ->update($attachment);
		return true;
	}
	
	/**
	 * Updates the last download time of a given attachment
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return bool
	 */
	public static function updateLastDownload($attachment)
	{
		if (!$attachment || !($attachment instanceof File_Models_Attachment)) {
			throw new Exception('The param is not an instance of File_Models_Attachment');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'file',
							'name'   => 'Attachment',
						 ))
						 ->setDbConnection($conn)
						 ->updateLastDownload($attachment);
		return true;
	}
	
	/**
	 * Deletes attachment file
	 * 
	 * @param File_Models_Attachment $attachment The attachment instance
	 * @return void
	 */
	private static function _deleteFile($attachment)
	{
		if ($attachment->path) {
			$file = APP_ROOT_DIR . DS . ltrim(str_replace('/', DS, $attachment->path), DS);
			if (file_exists($file)) {
				@unlink($file);
			}
		}
	}
	
	/**
	 * Uploads attachment
	 * 
	 * @param array $files Contains the file data, such as $_FILES['uploadedFiles']
	 * @return array|null
	 */
	private static function _uploadFile($files)
	{
		if (count($files) == 0) {
			return null;
		}
		
		$uploadDir = File_Services_Installer::DEFAULT_ATTACHMENTS_DIR .  '/' . date('Y/m') . '/' . Zend_Auth::getInstance()->getIdentity()->user_id;
		Core_Base_File::createDirectories($uploadDir, APP_ROOT_DIR);
		@chmod(APP_ROOT_DIR . $uploadDir, 0777);
		
		// Move the uploaded files to the upload folders
		$extension = explode('.', $files['name'][0]);
		$extension = strtolower($extension[count($extension) - 1]);
		$name	= basename($files['name'][0], '.' . $extension);
		$path	= '/' . ltrim(rtrim($uploadDir, '/'), '/') . '/' . Core_Base_String::clean($name, '-') . '-' . uniqid() . '.' . $extension;
		$target	= APP_ROOT_DIR . '/' . ltrim($path, '/');
		move_uploaded_file($files['tmp_name'][0], $target);
		$size = filesize($target);
		
		return array(
			'name'		=> $name,
			'extension' => $extension,
			'path'		=> $path,
			'size'		=> $size,
		);
	}
}
