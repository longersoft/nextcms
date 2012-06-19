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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Encryptor
{
	/**
	 * @var string
	 */
	private static $_secretKey = null;
	
	/**
	 * @var string
	 */
	private static $_encryptVector = 'myvector';
	
	/**
	 * Inits the encryptor
	 * 
	 * @return void
	 */
	private static function _init()
	{
		if (self::$_secretKey == null) {
			if (!Zend_Registry::isRegistered('db')) {
				Core_Services_Db::connect('slave');
			}
			self::$_secretKey = Core_Services_Config::get('core', 'secret_key');
		}
	}
	
	/**
	 * Encrypts given string
	 * 
	 * @param string $string The input string
	 * @return string
	 */
	public static function encrypt($string)
	{
		self::_init();
		
		$filter = new Zend_Filter_Encrypt(array(
			'adapter' => 'Mcrypt',
			'key'	  => self::$_secretKey,
			'vector'  => self::$_encryptVector,
		));
		$string = $filter->filter($string);
		
		// Use base64_encode() method so I can pass the encoded string in URL
		return rawurlencode(base64_encode($string));
	}
	
	/**
	 * Decrypts given string
	 * 
	 * @param string $string The encrypted string
	 * @return string
	 */
	public static function decrypt($string)
	{
		self::_init();
		
		$string = base64_decode(rawurldecode($string));
		$filter = new Zend_Filter_Decrypt(array(
			'adapter' => 'Mcrypt',
			'key'	  => self::$_secretKey,
			'vector'  => self::$_encryptVector,
		));
		$string = $filter->filter($string);
		
		// Use trim() to remove strange characters at the end of decrypted string
		return trim($string);
	}
}
