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
 * @subpackage	hooks
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Hooks_Explorer_Helper
{
	/**
	 * Gets the view helper instance
	 * 
	 * @return File_Hooks_Explorer_Helper
	 */
	public function helper()
	{
		return $this;
	}
	
	/**
	 * Checks if there is icon for given extension
	 * 
	 * @param string $extension The file extension
	 * @return bool
	 */
	public function findIcon($extension)
	{
		return file_exists(dirname(__FILE__) . DS . 'files' . DS . $extension . '.png');
	}
}
