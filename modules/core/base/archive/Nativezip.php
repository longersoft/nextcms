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

/**
 * This class use native zip extension to compress/decompress file
 */
class Core_Base_Archive_Nativezip extends Core_Base_Archive_Abstract
{
	/**
	 * @see Core_Base_Archive_Abstract::isSupported()
	 */
	public function isSupported()
	{
		return class_exists('ZipArchive');
	}

	/**
	 * @see Core_Base_Archive_Abstract::canCompress()
	 */
	public function canCompress()
	{
		return true;
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::canDecompress()
	 */
	public function canDecompress()
	{
		return true;
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::compress()
	 */
	public function compress($archive, $files, $options = array())
	{
		$overwrite = isset($options['overwrite']) ? $options['overwrite'] : true;
		$zip = new ZipArchive();		
		if ($zip->open($archive, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		$removePath = str_replace(DS, '/', $options['remove_path']);
		$removePath = rtrim($removePath, '/');
		foreach ($files as $file) {
			$name = rtrim(str_replace(DS, '/', $file), '/');
			$name = substr($name, strlen($removePath));
			$name = ltrim($name, '/');
			
			if (is_dir($file)) {
				$zip->addEmptyDir($name);
			} else {
				$zip->addFile($file, $name);
			}
		}
		
		$zip->close();
		return true;
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::decompress()
	 */
	public function decompress($archive, $destination, $options = array())
	{
		$zip = new ZipArchive();
		if ($zip->open($archive) !== true) {
			throw new Exception('Cannot open the file ' . $archive);
		}
		
		// Try to create the destination dir
		if (!is_dir($destination) && !mkdir($destination)) {
			throw new Exception('Cannot create the destination directory at ' . $destination);
		}
		$result = $zip->extractTo($destination);
		$zip->close();
		
		return $result;
	}
}
