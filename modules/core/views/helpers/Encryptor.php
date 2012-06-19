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

class Core_View_Helper_Encryptor
{
	/**
	 * Gets the view helper instance
	 * 
	 * @return Core_View_Helper_Encryptor
	 */
	public function encryptor()
	{
		return $this;
	}
	
	/**
	 * Encrypts given string
	 * 
	 * @param string $string The input string
	 * @return string
	 */
	public function encrypt($string)
	{
		return Core_Services_Encryptor::encrypt($string);
	}
	
	/**
	 * Decrypts given string
	 * 
	 * @param string $string The encrypted string
	 * @return string
	 */
	public function decrypt($string)
	{
		return Core_Services_Encryptor::decrypt($string);
	}
}
