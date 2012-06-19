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
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Mail
{
	/**
	 * Mail transport instance
	 * 
	 * @var Zend_Mail_Transport
	 */
	private static $_transport = null;
	
	/**
	 * Gets the built-in mail templates which are stored in
	 * the /modules/{ModuleName}/data/mails.php file
	 * 
	 * @param string $module The module name
	 * @param string $templateName
	 * @return array
	 */
	public static function getBuiltinTemplate($module, $templateName)
	{
		$template = array(
			'from_name'   => '',
			'from_email'  => '',
			'reply_name'  => '',
			'reply_email' => '',
			'subject'     => '',
			'content'     => '',
		);
		
		$dbTemplate = Core_Services_Config::get($module, $templateName, null);
		if ($dbTemplate == null) {
			// Try to retrieve the template from file
			$file = APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'data' . DS . 'mails.php';
			if (!file_exists($file)) {
				return $template;
			}
			$array = include $file;
			if (!is_array($array) || !isset($array[$templateName])) {
				return $template;
			}
			
			return $array[$templateName]; 
		} else {
			return Zend_Json::decode($dbTemplate);
		}
		
		return $template;
	}
	
	/**
	 * Gets mail transport
	 * 
	 * @return Zend_Mail_Transport
	 */
	public static function getMailTransport()
	{
		if (self::$_transport == null) {
			$mailOptions = Core_Services_Config::get('core', 'mail');
			$mailOptions = $mailOptions 
							? Zend_Json::decode($mailOptions) 
							: array(
									'protocol' => 'mail',
									'host'	   => '',
									'port'	   => '',
									'username' => '',
									'password' => '',
									'ssl'	   => '',
								);
			
			switch ($mailOptions['protocol']) {
				case 'mail':
					self::$_transport = new Zend_Mail_Transport_Sendmail();
					break;				
				case 'smtp':
					$options = array();
					if ($mailOptions['port']) {
						$options['port'] = $mailOptions['port'];
					}
					
					// Authentication settings
					if ($mailOptions['username'] && $mailOptions['password']) {
						$options['auth'] 	 = 'login';
						$options['username'] = $mailOptions['username'];
						$options['password'] = $mailOptions['password'];
					}
					
					// Security setting
					if ($mailOptions['ssl']) {
						$options['ssl'] = $mailOptions['ssl'];
					}
					
					self::$_transport = new Zend_Mail_Transport_Smtp($mailOptions['host'], $options);					
					break;
			}
		}
		
		return self::$_transport;
	}
}
