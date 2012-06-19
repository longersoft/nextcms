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

abstract class File_Services_File_Abstract
{
	/**
	 * Root directory
	 * 
	 * @var string
	 */
	protected $_rootDir;

	/**
	 * Connector
	 * 
	 * @var File_Services_Connector_Abstract
	 */
	protected $_connector;
	
	/**
	 * Connection parameters
	 * 
	 * @var array
	 */
	protected $_connectionParams = array();
	
	public function __construct($connectionParams = array())
	{
		$this->_connectionParams = $connectionParams;
	}
	
	/**
	 * Sets root directory
	 * 
	 * @param string $rootDir
	 * @return File_Services_File_Abstract
	 */
	public function setRootDir($rootDir)
	{
		$this->_rootDir = rtrim($rootDir, DS);
		return $this;		
	}
	
	/**
	 * Normalizes the directory's name
	 * 
	 * @param string $dir
	 * @return string
	 */
	protected function _normalizeDirName($dir)
	{
		if (!$dir) {
			return '';
		}
		if ($dir[0] == '.') {
			$dir = substr($dir, 1);
		}
		$dir = str_replace(DS, '/', $dir);
		return rtrim(ltrim($dir, '/'), '/');
	}
	
	/**
	 * Gets the connector
	 * 
	 * @return File_Services_Connector_Abstract
	 */
	public function getConnector()
	{
		return $this->_connector;
	}
	
	////////// ABSTRACT METHODS //////////
	
	/**
	 * Sets home directory
	 * 
	 * @param string $dir
	 * @return bool
	 */
	abstract public function setHomeDir($dir);
	
	/**
	 * Gets the list of files in the given directory
	 * 
	 * @param string $dir
	 * @param array criteria Consists of the following members:
	 * 		- name (string): Search for the files/directories which have the name look like the value of this member
	 * 		- dirs_only (bool): Can be true, false, null.
	 * 			If true, returns the list of directories only. If false, returns only the the list of files.
	 * 			If nulls, return the list of all directories and files.
	 * 		- hidden_files: If true, includes the hidden files in the list
	 * 		- case_sensitive:
	 * 		- regular_expression
	 * 		- recurse: If true, search for the sub-directories recursively
	 * @return array Returns an array of files. Each item contains all the information as described in 
	 * getFileInfo() method.
	 */
	public function getFiles($dir, $criteria = array())
	{
		$criteria = array_merge(array(
						'name'				 => null,
						'dirs_only'			 => true,
						'hidden_files'		 => true,
						'case_sensitive'	 => false,
						'regular_expression' => false,
						'recurse'			 => false,
					), $criteria);
		
		if ($criteria['name']) {
			if ($criteria['regular_expression']) {
				$criteria['_pattern'] = self::_convertToRegExp($criteria['name']);
				if ($criteria['case_sensitive']) {
					$criteria['_pattern'] .= "i";
				}
			}
		}
		
		return $this->_getFiles($dir, $criteria);
	}
	
	abstract protected function _getFiles($dir, $criteria = array());
	
	/**
	 * Helper function to convert a simple pattern to a regular expression
	 * 
	 * @param string $pattern
	 * @return string
	 */
	private static function _convertToRegExp($pattern)
	{
		$regExp = "^";
		$char   = "";
		$length = strlen($pattern);
		for ($i = 0; $i < $length; $i++) {
			$char = $pattern[$i];
			switch ($char) {
				case '\\':
					$regExp = $regExp . $char;
					$i++;
					$regExp = $regExp . $pattern[$i];
					break;
				case '*':
					$regExp = $regExp . ".*"; 
					break;
				case '?':
					$regExp = $regExp . "."; 
					break;
				case '$':
				case '^':
				case '/':
				case '+':
				case '.':
				case '|':
				case '(':
				case ')':
				case '{':
				case '}':
				case '[':
				case ']':
					$regExp = $regExp . "\\";
				default:
					$regExp = $regExp . $char;
			}
		}
		return "/" . $regExp . "$/";
	}
	
	/**
	 * Gets file information
	 * 
	 * @param string $file The file's name
	 * @param string $dir The sub-path contains the file
	 * @param bool $showHiddenFiles
	 * @return array An array with the following keys:
	 * 		- directory: Indicate if the file is a directory
	 * 		- size
	 * 		- modified
	 *  	- readableModified
	 * 		- name
	 * 		- path
	 *  	- children
	 *  	- parentDir
	 *  	- perms: File permissions in string (rw-r--r--, for example)
	 * With these information, I can easily populate data for the controls in the client side
	 * using dojox.data.FileStore
	 */
	abstract public function getFileInfo($file, $dir, $dirsOnly = true, $showHiddenFiles = true);
	
	/**
	 * Creates sub-directory
	 * 
	 * @param string $parent The parent directory
	 * @param string $dir Name of new directory
	 * @return bool
	 */
	abstract public function createSubDir($parent, $dir);
	
	/**
	 * Deletes directory
	 * 
	 * @param string $dir
	 * @return bool
	 */
	abstract public function deleteDir($dir);
	
	/**
	 * Deletes directory rescursively
	 * 
	 * @param string $dir
	 * @return bool
	 */
	abstract public function deleteDirRescursive($dir);
	
	/**
	 * Uploads file to given directory
	 * 
	 * @param string $file
	 * @param string $dir
	 * @param bool $overwrite
	 * @return array Returns file information
	 */
	abstract public function uploadFile($file, $dir, $overwrite = false);
	
	/**
	 * Uploads entire directory to given directory recursively
	 * 
	 * @param string $source
	 * @param string $target
	 * @param bool $overwrite
	 * @param bool $includeSourceDir If true, and the source is a directory, it will upload entire directory, 
	 * including the source directory
	 * @return bool
	 */
	abstract public function uploadDir($source, $target, $overwrite = true, $includeSourceDir = true);
	
	/**
	 * Deletes file
	 * 
	 * @param string $file
	 * @return bool
	 */
	abstract public function deleteFile($file);
	
	/**
	 * Renames file
	 * 
	 * @param string $currentName
	 * @param string $newName New name of file
	 * @param string $path The sub-path contains the file
	 * @return bool
	 */
	abstract public function renameFile($currentName, $newName, $path);
	
	/**
	 * Copies file
	 * 
	 * @param string $source The sub-path of source
	 * @param string $target The sub-path of target
	 * @param bool $overwrite
	 * @param bool $includeSourceDir If true, and the source is a directory, it will copy all files including the source directory
	 * @return bool
	 */
	abstract public function copyFile($source, $target, $overwrite = true, $includeSourceDir = true);
	
	/**
	 * Moves file
	 * 
	 * @param string $source The sub-path of source
	 * @param string $target The sub-path of target
	 * @param bool $overwrite
	 * @return bool
	 */
	abstract public function moveFile($source, $target, $overwrite = true);
	
	/**
	 * Perpares the file for downloading
	 * 
	 * @param string $file Sub-path of file
	 * @return string Path of the file which is ready to download
	 */
	abstract public function downloadFile($file);
	
	/**
	 * Downloads files on the remote server to the local server
	 * 
	 * @param array $files Array of files
	 * @param string $destination The full path of destination file on local
	 * @param bool $overwrite
	 * @return bool
	 */
	abstract public function downloadFiles($files, $destination, $overwrite = true);
	
	/**
	 * Sets the permission to file
	 * 
	 * @param string $path The sub-path of file
	 * @param string $permissionDecimal Permission in decimal
	 * @param bool $recurse
	 * @return bool
	 */
	abstract public function setPermission($path, $permissionDecimal, $recurse = false);
}
