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
 * @subpackage	services
 * @since		1.0
 * @version		2011-12-11
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Language
{
	/**
	 * Deletes a given language item from language file
	 * 
	 * @param string $file The file's path
	 * @param string $path Path to new language item in the format of a.b.c
	 * @return bool
	 */
	public static function deleteLanguageItem($file, $path)
	{
		$file = self::_getLanguageFile($file);
		if (!file_exists($file)) {
			return false;
		}
		
		// Get the content of file
		$array = Zend_Json::decode(file_get_contents($file));
		$paths = explode('.', $path);
		
		$str   = 'unset($array["' . implode('"]["', $paths) . '"]);';
		eval($str);
		
		$content = Zend_Json::prettyPrint(Zend_Json::encode($array));
		file_put_contents($file, $content);
		
		return true;
	}
	
	/**
	 * Finds language files of extensions of a given module
	 * 
	 * @param string $module The module's name
	 * @param string $extensionType The type of extension. Can be 'hooks', 'plugins', 'tasks', 'widgets'
	 * @return array 
	 */
	public static function findExtensionLanguages($module, $extensionType)
	{
		$dir	 = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . $extensionType;
		$subDirs = Core_Base_File::getSubDirectories($dir);
		if (count($subDirs) == 0) {
			return null;
		}
		$languages = array();
		foreach ($subDirs as $extensionDir) {
			$languages[$extensionDir] = self::findLanguages($dir . DS . $extensionDir);
		}
		return $languages;
	}
	
	/**
	 * Finds language files in a given directory
	 * 
	 * @param string $dir Path of directory
	 * @return array
	 */
	public static function findLanguages($dir)
	{
		if (!file_exists($dir)) {
			return array();
		}
		$languages   = array();
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $dir) {
			if ($dir->isDot() || $dir->isDir()) {
				continue;
			}
			$fileName = $dir->getFilename();
			if (preg_match('/^([a-z]+)_[A-Z]+(\.)json$/', $fileName)) {
				$languages[] = $fileName;
			}
		}
		return $languages;
	}
	
	/**
	 * Adds/updates a language item
	 * 
	 * @param string $file The file's path
	 * @param string $path Path to new language item in the format of a.b.c
	 * @param string $text The text
	 * @return bool
	 */
	public static function setLanguageItem($file, $path, $text)
	{
		$file = self::_getLanguageFile($file);
		if (!file_exists($file)) {
			return false;
		}
		
		// Get the content of file
		$array = Zend_Json::decode(file_get_contents($file));
		$paths = explode('.', $path);
		
		$str   = '$array["' . implode('"]["', $paths) . '"] = $text;';
		eval($str);
		
		$content = Zend_Json::prettyPrint(Zend_Json::encode($array));
		file_put_contents($file, $content);
		
		return true;
	}
	
	/**
	 * Gets the full path of language file
	 * 
	 * @param string $file
	 * @return string
	 */
	private static function _getLanguageFile($file)
	{
		$file = str_replace('/', DS, $file);
		$file = ltrim($file, DS);
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $file;
		return $file;
	}
}
