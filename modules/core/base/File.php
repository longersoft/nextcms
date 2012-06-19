<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	base
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_File
{
	/**
	 * Creates directories and sub-directories if need
	 * 
	 * @param string $path The relative path
	 * @param string $root The root directory which already exists.
	 * @return bool
	 */
	public static function createDirectories($path, $root = DS)
	{
		$path		= ltrim(str_replace('/', DS, $path), DS);
		$currentDir = rtrim($root, DS);
		$subDirs	= explode(DS, $path);
		if ($subDirs == null) {
			return;
		}
		foreach ($subDirs as $dir) {
			$currentDir .= DS . $dir;
			if (!file_exists($currentDir)) {
				if (mkdir($currentDir) === false) {
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Deletes directory and all its sub-directories recursively
	 * 
	 * @param string $dir
	 * @return bool
	 */
	public static function deleteDirectory($dir) 
	{
		$result = true;
		if (is_dir($dir)) {
			$dir 	 = (substr($dir, -1) != DS) ? $dir . DS : $dir;
			$openDir = opendir($dir);
			while ($file = readdir($openDir)) {
				if (!in_array($file, array(".", ".."))) {
					if (!is_dir($dir . $file)) {
						$result = $result && @unlink($dir . $file);
					} else {
						$result = $result && self::deleteDirectory($dir . $file);
					}
				}
			}
			closedir($openDir);
			$result = $result && @rmdir($dir);
		}

		return $result;
	}	
	
	/**
	 * Gets sub-directori(es) of a directory, not including the directory which
	 * its name is one of ".", ".." or ".svn"
	 * 
	 * @param string $dir Path to the directory
	 * @return array
	 */
	public static function getSubDirectories($dir)
	{
		if (!file_exists($dir)) {
			return array();
		}

		$subDirs 	 = array();
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $dir) {
			if ($dir->isDot() || !$dir->isDir()) {
				continue;
			}
			$dir = $dir->getFilename();
			if ($dir == '.svn') {
				continue;
			}
			$subDirs[] = $dir;
		}
		return $subDirs;
	}
}
