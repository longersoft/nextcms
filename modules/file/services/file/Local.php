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

class File_Services_File_Local extends File_Services_File_Abstract
{
	public function __construct($connectionParams = array())
	{
		$this->_connector = new File_Services_Connector_Local();
		$this->_rootDir	  = APP_ROOT_DIR;
	}
	
	/**
	 * @see File_Services_File_Abstract::setHomeDir()
	 */
	public function setHomeDir($dir)
	{
		return chdir($dir);
	}
	
	/**
	 * @see File_Services_File_Abstract::_getFiles()
	 */
	protected function _getFiles($dir, $criteria = array())
	{
		$rootDir = $this->_rootDir . '/' . $dir;
		$files	 = array();
		if (!is_dir($rootDir)) {
			return $files;
		}
		
		if ($handle = opendir($rootDir)) {
			while ($file = readdir($handle)) {
				if ($file === false || $file == '..' || $file == '.') {
					continue;
				}
				
				$path	  = $dir . '/' . $file;
				$fileInfo = $this->getFileInfo($file, $dir, $criteria['dirs_only'], $criteria['hidden_files']);
				
				// Check if the file matches with the name or not
				$match = true;
				if ($criteria['name']) {
					if ($criteria['regular_expression']) {
						$match = preg_match($criteria['_pattern'], $fileInfo['name']);
					} else {
						// If not use regular expression, use strpos() for faster
						$match = ($criteria['case_sensitive']) 
									? strpos($fileInfo['name'], $criteria['name']) !== false
									: stripos($fileInfo['name'], $criteria['name']) !== false;
					}
				}
				
				if ($match) {
					if ($criteria['dirs_only'] === null
						|| ($criteria['dirs_only'] === true && $fileInfo['directory'] == true)
						|| ($criteria['dirs_only'] === false && $fileInfo['directory'] == false))
					{
						if ($criteria['hidden_files'] || $fileInfo['name'][0] !== '.') {
							$files[] = $fileInfo;
						}
					}
				}
				
				// Search in sub-directories recursively if requested
				if (is_dir($this->_rootDir . '/' . $path) && $criteria['recurse']) {
					if ($criteria['hidden_files'] || $fileInfo['name'][0] !== '.') {
						$files = array_merge($files, $this->_getFiles($path, $criteria));
					}
				}
			}
			closedir($handle);
		}

		return $files;
	}
	
	/**
	 * @see File_Services_File_Abstract::getFileInfo()
	 */
	public function getFileInfo($file, $dir, $dirsOnly = true, $showHiddenFiles = true)
	{
		$path = $file;
		if ($dir != '.' && $dir != './') {
			$path = $dir . '/' . $file;
		}

		$fullPath		  = $this->_rootDir . '/' . $path;
		$rootPath		  = realPath($this->_rootDir);
		$resolvedDir	  = realPath($this->_rootDir . '/' . $dir);
		$resolvedFullPath = realPath($fullPath);

		if (strcmp($rootPath, $resolvedDir) === 0) {
			$dir = '.';
		} else {
			$dir = substr($resolvedDir, (strlen($rootPath) + 1), strlen($resolvedDir));
			$dir = './' . str_replace('\\', '/', $dir);
		}
		if (strcmp($rootPath, $resolvedFullPath) === 0) {
			$path = '.';
		} else {
			$path = substr($resolvedFullPath, (strlen($rootPath) + 1), strlen($resolvedFullPath));
			$path = './' . str_replace('\\', '/', $path);
		}
		
		$stat	  = stat($fullPath);
		$pathInfo = pathinfo($fullPath);
		$fileInfo = array(
			'name'			   => $file,
			'path'			   => $path,
			'parentDir'		   => $dir,
			'directory'		   => is_dir($fullPath),
			'size'			   => filesize($fullPath),
			'modified'		   => filemtime($fullPath), 	// $stat[9],
			'readableModified' => '',
			'extension'		   => isset($pathInfo['extension']) ? $pathInfo['extension'] : (is_dir($fullPath) ? "_dir" : "_file"),
			'perms'			   => @decoct(@fileperms($fullPath) & 0777),
		);
		
		if ($fileInfo['modified'] !== false) {
			$fileInfo['readableModified'] = date('Y-m-d H:i:s', $fileInfo['modified']);
		}
		
		// Determine the children
		if (is_dir($fullPath)) {
			$fileInfo['children'] = array();
			$handle = opendir($fullPath);
			while ($f = readdir($handle)) {
				if ($f) {
					if ($f != '..' && $f != '.') {
						if ($showHiddenFiles || $f[0] != '.') {
							if ($dirsOnly === null 
								|| ($dirsOnly === false && !is_dir($fullPath . '/' . $f))
								|| ($dirsOnly === true && is_dir($fullPath . '/' . $f)))
							{
								$fileInfo['children'][] = $f;
							}
						}
					}
				}
			}
			closedir($handle);
		}
		
		return $fileInfo;
	}
	
	/**
	 * @see File_Services_File_Abstract::createSubDir()
	 */
	public function createSubDir($parent, $dir) 
	{
		$parent = $this->_normalizeDirName($parent);
		$dir	= $this->_normalizeDirName($dir);
		$newDir = $this->_rootDir . '/' . $parent . '/' . $dir;
		if (is_dir($newDir)) {
			return true;
		} elseif (mkdir($newDir, 0777)) {
			return true;
		}
		return false;
	}
	
	/**
	 * @see File_Services_File_Abstract::deleteDir()
	 */
	public function deleteDir($dir) 
	{
		$dir = $this->_normalizeDirName($dir);
		$dir = $this->_rootDir . '/' . $dir;
		
		if (file_exists($dir) && is_dir($dir)) {			
			if (rmdir($dir)) {
				return true;
			}	
		}
		return false;
	}
	
	/**
	 * @see File_Services_File_Abstract::deleteDirRescursive()
	 */
	public function deleteDirRescursive($dir) 
	{
		$dir = $this->_normalizeDirName($dir);		
		$dir = $this->_rootDir . '/' . $dir;
		return $this->_deleteSubDirRescursive($dir);
	}
	
	private function _deleteSubDirRescursive($dir) 
	{
		if (is_dir($dir)) {
			$openDir = opendir($dir);
			while ($file = readdir($openDir)) {
				if (!in_array($file, array(".", ".."))) {
					if (!is_dir($dir . '/' . $file)) {
						$result = @unlink($dir . '/' . $file);
						if ($result === false) {
							return false;
						}
					} else {
						$this->_deleteSubDirRescursive($dir . '/' . $file);
					}
				}
			}
			closedir($openDir);
			return @rmdir($dir);
		}

		return true;
	}
	
	/**
	 * @see File_Services_File_Abstract::uploadFile()
	 */
	public function uploadFile($file, $dir, $overwrite = false)
	{
		$dir  = $this->_normalizeDirName($dir);
		
		// Get file info
		$info = pathinfo($file);
		$dest = $this->_rootDir . '/' . $dir . '/' . $info['basename'];
		
		if (!file_exists($dest) || $overwrite) {
			return @copy($file, $dest);
		}
		return true;
	}
	
	/**
	 * @see File_Services_File_Abstract::uploadDir()
	 */
	public function uploadDir($source, $target, $overwrite = true, $includeSourceDir = true)
	{
		// $source is the full path, so I need to convert it to the relative path (such as ./folder/subFolder)
		$source  = $this->_normalizeDirName($source);
		$rootDir = $this->_normalizeDirName($this->_rootDir);
		$source  = substr($source, strlen($rootDir));
		$source  = './' . $this->_normalizeDirName($source);
		
		return $this->copyFile($source, $target, $overwrite, $includeSourceDir);
	}
	
	/**
	 * @see File_Services_File_Abstract::deleteFile()
	 */
	public function deleteFile($file)
	{
		$file = $this->_normalizeDirName($file);
		$file = $this->_rootDir . '/' . $file;
		return @unlink($file);
	}
	
	/**
	 * @see File_Services_File_Abstract::renameFile()
	 */
	public function renameFile($currentName, $newName, $path)
	{
		$currentFile = $this->_rootDir . '/' . $this->_normalizeDirName($path) . '/' . $currentName;
		$newFile	 = $this->_rootDir . '/' . $this->_normalizeDirName($path) . '/' . $newName;
		return rename($currentFile, $newFile);
	}
	
	/**
	 * @see File_Services_File_Abstract::copyFile()
	 */
	public function copyFile($source, $target, $overwrite = true, $includeSourceDir = true)
	{
		$fullSourcePath = $this->_rootDir . '/' . $this->_normalizeDirName($source);
		$fullTargetPath = $this->_rootDir . '/' . $this->_normalizeDirName($target);
		
		$info = pathinfo($fullSourcePath);
		if ($info['dirname'] == $fullTargetPath) {
			return false;
		}
		if (substr($fullTargetPath, 0, strlen($fullSourcePath) - 1) == $fullSourcePath) {
			return false;
		}
		
		// If source is just a file
		if (!is_dir($fullSourcePath)) {
			$dest = (is_dir($fullTargetPath) && $includeSourceDir) ? $fullTargetPath . '/' . $info['basename'] : $fullTargetPath;
			if (!file_exists($dest) || $overwrite) {
				return @copy($fullSourcePath, $dest);
			} else {
				return true;
			}
		}
		
		// Create the directory in the target if it is requested
		$info  = pathinfo($source);
		if ($includeSourceDir && !file_exists($fullTargetPath . '/' . $info['basename'])) {
			@mkdir($fullTargetPath . '/' . $info['basename'], 0777);
		}
		
		// List all files and sub-directories, including the source itself
		$files = $this->getFiles($source, array(
											'name'		   => null,
											'dirs_only'	   => null,
											'hidden_files' => true,
											'recurse'	   => true,
										));
		
		$prefixTarget = $includeSourceDir ? $this->_rootDir . '/' . $this->_normalizeDirName($target) . '/' . $info['basename']
										  : $this->_rootDir . '/' . $this->_normalizeDirName($target);
		foreach ($files as $fileInfo) {
			$dest = substr($fileInfo['path'], strlen($source));
			$dest = $prefixTarget . '/' . $this->_normalizeDirName($dest);
			
			if ($fileInfo['directory']) {
				// Create directory
				if (!file_exists($dest)) {
					@mkdir($dest, 0777);
				}
			} else {
				// Copy file
				if (!file_exists($dest) || $overwrite) {
					@copy($this->_rootDir . '/' . $this->_normalizeDirName($fileInfo['path']), $dest);
				}
			}
		}
		
		return true;
	}
	
	/**
	 * @see File_Services_File_Abstract::moveFile()
	 */
	public function moveFile($source, $target, $overwrite = true)
	{
		// Copy first
		$copyResult = $this->copyFile($source, $target, $overwrite);
		if ($copyResult === false) {
			return false;
		}
		// Then delete the source
		if (is_dir($this->_rootDir . '/' . $this->_normalizeDirName($source))) {
			return $this->deleteDirRescursive($source);
		}
		return $this->deleteFile($source);
	}
	
	/**
	 * @see File_Services_File_Abstract::downloadFile()
	 */
	public function downloadFile($file)
	{
		return $this->_rootDir . '/' . $this->_normalizeDirName($file);
	}
	
	/**
	 * @see File_Services_File_Abstract::downloadFiles()
	 */
	public function downloadFiles($files, $destination, $overwrite = true)
	{
		if (!is_array($files)) {
			throw new Exception('The first param is not an array');
		}
		
		// $destination is the full path, so I need to convert it to the relative path (such as ./folder/subFolder)
		$destination = $this->_normalizeDirName($destination);
		$destination = substr($destination, strlen($this->_normalizeDirName($this->_rootDir)));
		$destination = './' . $this->_normalizeDirName($destination);
		
		$result = true;
		foreach ($files as $file) {
			$result = $result && $this->copyFile($file, $destination, $overwrite);
		}
		
		return $result;
	}
	
	/**
	 * @see File_Services_File_Abstract::setPermission()
	 */
	public function setPermission($path, $permissionDecimal, $recurse = false)
	{
		$fullPath = $this->_rootDir . '/' . $this->_normalizeDirName($path);
		$result   = true;
		
		if (is_link($fullPath) || is_file($fullPath)) {
			// The reason why I pass the permission in decimal format is here:
			//		chmod($fullPath, $decimal) 						 => work
			//		chmod($fullPath, 777) and chmod($fullPath, 0777) => give me different results
			$result = @chmod($fullPath, $permissionDecimal);
		} elseif (is_dir($fullPath)) {
			$handle = opendir($fullPath);
			if ($handle === false) {
				return false;
			}
			
			// Becareful when setting the permisson to the directory. 
			// I have to set the execute bit in order to make the directory readable
			$binary = decbin($permissionDecimal);
			
			// Set the 1st, 2nd, 3rd x bit to 1
			$binary = substr_replace($binary, '1', 2, 1);
			$binary = substr_replace($binary, '1', 5, 1);
			$binary = substr_replace($binary, '1', 8, 1);
			$result = @chmod($fullPath, bindec($binary));
			
			if ($recurse) {
				while (($file = readdir($handle)) !== false) {
					if ($file == '..' || $file == '.') {
						continue;
					}
					$fileFullPath = $fullPath . '/' . $file;
					if (!file_exists($fileFullPath)) {
						continue;
					}
					$result = $this->setPermission($path . '/' . $file, $permissionDecimal, $recurse);
				}
			}
			closedir($handle);
		}
		
		return $result;
	}
}
