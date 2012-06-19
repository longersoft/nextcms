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
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_FileFormatter
{
	/**
	 * Returns the view helper instance
	 * 
	 * @return Core_View_Helper_FileFormatter
	 */
	public function fileFormatter()
	{
		return $this;
	}
	
	/**
	 * Formats file size
	 * 
	 * @param int $size File size in bytes
	 * @param string $zeroString The string that will be returned if the file size is 0 byte
	 * @return string Returns the file size in larger unit, such as MB, GB, etc
	 */
	public function formatSize($size, $zeroString = '')
	{
		if ($size == 0) {
			return ($zeroString == null) ? '' : $zeroString;
		}
		
		switch (true) {
		case ($size >= 1073741824):
			return number_format($size / 1073741824, 2, '.', '') . ' Gb';
		case ($size >= 1048576):
			return number_format($size / 1048576, 2, '.', '') . ' Mb';
		case ($size >= 1024):
			return number_format($size / 1024, 0) . ' Kb';
		default:
			return number_format($size, 0) . ' bytes';
		}
		return $size;
	}
}
