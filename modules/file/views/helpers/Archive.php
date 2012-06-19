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
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * @todo Use Zend_Filter_Compress
 */
class File_View_Helper_Archive
{
	/**
	 * Array of archive extensions
	 * 
	 * @var array
	 */
	private static $_EXTENSIONS = array('zip');
	
	/**
	 * @return File_View_Helper_Archive
	 */
	public function archive()
	{
		return $this;
	}
	
	/**
	 * Lists decompressable extensions
	 * 
	 * @return string
	 */
	public function getDecompressableExts()
	{
		$extensions = array();
		foreach (self::$_EXTENSIONS as $ext) {
			$adapter = Core_Base_Archive::factory($ext);
			if ($adapter && $adapter->isSupported() && $adapter->canDecompress()) {
				$extensions[] = $ext;
			}
		}
		return implode(',', $extensions);
	}
	
	/**
	 * Lists compressable extensions
	 * 
	 * @return string
	 */
	public function getCompressableExts()
	{
		$extensions = array();
		foreach (self::$_EXTENSIONS as $ext) {
			$adapter = Core_Base_Archive::factory($ext);
			if ($adapter && $adapter->isSupported() && $adapter->canCompress()) {
				$extensions[] = $ext;
			}
		}
		return implode(',', $extensions);
	}
}
