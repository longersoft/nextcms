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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Services_Explorer
{
	/**
	 * Compress a file to given directory
	 * 
	 * @param string $adapter Compress adapter. Can be "zip", "tar", etc.
	 * @param File_Models_Connection $connection
	 * @param array $sourceFiles
	 * @param string $destination
	 * @param bool $overwrite
	 * @return bool
	 */
	public static function compress($adapter, $connection, $sourceFiles, $destination, $overwrite = false)
	{
		$pathInfo = pathinfo($destination);
		$archiver = Core_Base_Archive::factory($adapter);
		if (!$archiver || !$archiver->isSupported() || !$archiver->canCompress()) {
			return false;
		}
		
		$file = File_Services_File::factory($connection->type, $connection->getProperties());
		
		// Prepare the temp folders
		$tempDir		  = 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
		$tempCompressDir  = $tempDir . DS . 'compress_' . uniqid();
		$tempCompressPath = APP_TEMP_DIR . DS . $tempCompressDir;
		Core_Base_File::createDirectories($tempCompressDir, APP_TEMP_DIR);
		
		$tempDir		  = APP_TEMP_DIR . DS . $tempDir;
		@chmod($tempDir, 0777);
		@chmod($tempCompressPath, 0777);
		
		// Download the source to local
		if ($file->downloadFiles($sourceFiles, $tempCompressPath, true) == false) {
			return false;
		}
		
		// Compress the temp dir
		$tempCompressFile = $tempDir . DS . $pathInfo['basename'];
		$localFile = new File_Services_File_Local();
		$localFile->setRootDir(APP_ROOT_DIR);
		$fileInfoArray = $localFile->getFiles('./' . basename(APP_TEMP_DIR) . '/' . $tempCompressDir, array(
											'name'		   => null,
											'dirs_only'	   => null,
											'hidden_files' => true,
											'recurse'	   => true,
										));
		$files = array();
		foreach ($fileInfoArray as $info) {
			$files[] = APP_ROOT_DIR . '/' . ltrim(ltrim($info['path'], '.'), '/');
		}
		if ($archiver->compress($tempCompressFile, $files, array('overwrite' => true, 'remove_path' => $tempCompressPath)) == false) {
			return false;
		}
										
		// Upload the compressed file to the server
		return $file->uploadFile($tempCompressFile, $pathInfo['dirname'], $overwrite);
	}
	
	/**
	 * Cleans up the system when disconnecting
	 * 
	 * @param File_Models_Connection|null $connection
	 * @return bool
	 */
	public static function disconnect($connection = null)
	{
		// Remove the temp folder created when doing some actions such as viewing, editing files
		$tempDir = 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
		$tempDir = APP_TEMP_DIR . DS . $tempDir;
		return Core_Base_File::deleteDirectory($tempDir);
	}
	
	/**
	 * Extracts an archive file to given directory
	 * 
	 * @param File_Models_Connection $connection
	 * @param string $source
	 * @param string $destination
	 * @param bool $overwrite
	 * @return bool
	 */
	public static function extract($connection, $source, $destination, $overwrite = false)
	{
		$pathInfo  = pathinfo($source);
		$extension = strtolower($pathInfo['extension']);
		$archiver  = Core_Base_Archive::factory($extension);
		if (!$archiver || !$archiver->isSupported() || !$archiver->canDecompress()) {
			return false;
		}
		
		$file = File_Services_File::factory($connection->type, $connection->getProperties());
		
		// Download the file to local
		$downloadedFile = $file->downloadFile($source);
		
		// Prepare the temp folders
		$tempDir		= 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
		$tempExtractDir = $tempDir . DS . 'extract_' . uniqid();
		Core_Base_File::createDirectories($tempExtractDir, APP_TEMP_DIR);
		
		$tempDir		= APP_TEMP_DIR . DS . $tempDir;
		$tempExtractDir = APP_TEMP_DIR . DS . $tempExtractDir;
		@chmod($tempDir, 0777);
		@chmod($tempExtractDir, 0777);
		
		// Copy the downloaded file to the temp foler
		$tempFile = $tempDir . DS . $pathInfo['basename'];
		@copy($downloadedFile, $tempFile);
		
		// Extract file to the temp folder
		$result = $archiver->decompress($tempFile, $tempExtractDir);
		if ($result) {
			// Upload entire extracted dir to the server
			$result = $file->uploadDir($tempExtractDir, $destination, $overwrite, false);
		}
		
		return $result;
	}
}
