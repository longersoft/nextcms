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
 * @version		2012-02-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_String
{
	/**
	 * Generates slug string
	 * 
	 * @param string $string
	 * @param string $separator
	 * @param string $locale
	 * @return string
	 */
	public static function clean($string, $separator = '-', $locale = null)
	{
		$file = $locale 
				? APP_ROOT_DIR . DS . 'data' . DS . 'locale' . DS . $locale . '.php'
				: APP_ROOT_DIR . DS . 'data' . DS . 'slug.php';
		$specialCharacters = array();
		
		if (file_exists($file)) {
			$data = include $file;
			if (is_array($data) && isset($data['slug'])) {
				$specialCharacters = $data['slug'];
			}
		}
		
		foreach ($specialCharacters as $key => $value) {
			$string = preg_replace('/' . $key . '/i', $value, $string);
		}
		
		if (function_exists('iconv')) {
			$string = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $string);
		}
		
		// Make lower string
		if (function_exists('mb_strtolower')) {
			$string = mb_strtolower(trim($string, '-'));
		} else {
			$string = strtolower(trim($string, '-'));
		}
		
		$string = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $string);
		$string = preg_replace('/[\/_|+ -]+/', $separator, $string);
	
		return $string;
	}
	
	/**
	 * Generates unique hash
	 * 
	 * @param string $input
	 * @param string $salt
	 * @param int $saltLength The length of salt
	 * @return string
	 */
	public static function generateHash($input = '', $salt = null, $saltLength = 8)
	{
		if ($salt == null) {
			$salt = md5(uniqid(rand(), true));
		}
		$salt = substr($salt, 0, $saltLength);
		return $salt . sha1($salt . $input);
	}
	
	/**
	 * Generates random string which can be used as a password, secret key, etc
	 * 
	 * @param int $length The length of string
	 * @return string
	 */
	public static function generateRandomString($length = 8)
	{
		// Remove "l" character to avoid getting mistaken with "1" digit
		$chars	   = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
		$maxLength = strlen($chars);
		
		if ($length > $maxLength) {
			$length = $maxLength;
		}
		
		$i = 0;
		$string = '';
		while ($i < $length) {
			// Pick a random character
			$char = substr($chars, mt_rand(0, $maxLength -1 ), 1);
			
			// Check if the character is already used in the string
			if (!strstr($string, $char)) {
				$string .= $char;
			 	$i++;
			}
		}
		
		return $string;
	}
	
	/**
	 * Creates sub-string from given string
	 * 
	 * @param string $string The input string
	 * @param int $length The length of new string
	 * @param string $suffix The suffix that will be added to the end of new string string
	 * @return string
	 */
	public static function sub($string, $length, $suffix = '...')
	{
		if (!$string) {
			return '';
		}
		if (strlen($string) <= $length) {
			return $string;
		}
		
		// Check if module mbstring is already installed or not
		if (function_exists('mb_substr')) {
			return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
		} else {
			return substr($string, 0, $length) . $suffix;
		}
	}
}
