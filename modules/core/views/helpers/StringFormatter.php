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
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_StringFormatter extends Zend_View_Helper_Abstract
{
	/**
	 * Returns the view helper instance
	 * 
	 * @return Core_View_Helper_StringFormatter
	 */
	public function stringFormatter()
	{
		return $this;
	}
	
	/**
	 * Sub string
	 * 
	 * @param string $string
	 * @param int $length
	 * @param string $suffix The string that will be added to the end of the input string
	 * @return string
	 */
	public function sub($string, $length = 100, $suffix = '...')
	{
		return Core_Base_String::sub($string, $length, $suffix);
	}
}
