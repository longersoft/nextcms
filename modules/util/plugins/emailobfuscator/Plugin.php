<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		util
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-03-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Plugins_Emailobfuscator_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * Email pattern
	 * 
	 * @var string
	 */
	const MAIL_PATTERN = '/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]+\b(?!([^<]+)?>)/i';
	
	/**
	 * Pattern represents <a href="mailto:EmailAddress"></a>
	 * 
	 * @var string
	 */
	const MAIL_TO_PATTERN = '/(<a[^>]*)(href=")(mailto:)([^"]+)([^>]*>)/';
	
	/**
	 * @var string
	 */
	const MAIL_TO_CSS_CLASS = 'utilPluginsEmailobfuscatorMailTo';
	
	/**
	 * @var bool
	 */
	private $_enabled = false;

	/**
	 * @see Zend_Controller_Plugin_Abstract::postDispatch()
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_enabled = false;
			return;
		}
		$layout = Zend_Layout::getMvcInstance();
		if (!$layout || 'admin' == $layout->getLayout()) {
			$this->_enabled = false;
			return;
		}
		
		$this->_enabled = true;
		
		// Append the script to turn the encoded email address to normal at the and of page
		$this->append();
	}
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown()
	{
		if ($this->_enabled) {
			$response = $this->getResponse();
			$body	  = $response->getBody();
			
			// Find email address
			$body = preg_replace_callback(self::MAIL_TO_PATTERN, 'Util_Plugins_Emailobfuscator_Plugin::obfuscateMailTo', $body);
			$body = preg_replace_callback(self::MAIL_PATTERN, 'Util_Plugins_Emailobfuscator_Plugin::obfuscateEmail', $body);
		}
	}
	
	/**
	 * Appends Javascript at the end of page
	 * 
	 * @return void
	 */
	public function appendAction()
	{
		$this->view->assign('mailToClass', self::MAIL_TO_CSS_CLASS);
	}
	
	/**
	 * Obfuscates email address
	 * 
	 * @param array $matches
	 * @return string
	 */
	public static function obfuscateEmail($matches)
	{
		$email = $matches[0];
		return '<script type="text/javascript">'
			 . 'document.write("' . str_rot13($email) . '".replace(/[a-zA-Z]/g, function(c) { return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26); }));'
			 . '</script>';
	}
	
	/**
	 * Obfuscates mailto link
	 * 
	 * @param array $matches
	 * @return string
	 */
	public static function obfuscateMailTo($matches)
	{
		if (strpos($matches[1], "class=\"") !== false) {
			$matches[1] = str_replace("class=\"", "class=\"" . self::MAIL_TO_CSS_CLASS . " ", $matches[1]);
		}
		if (strpos($matches[5], "class=\"") !== false) {
			$matches[5] = str_replace("class=\"", "class=\"" . self::MAIL_TO_CSS_CLASS . " ", $matches[5]);
		}
		
		// mailto: EmailAddress
		$mailTo = $matches[3] . $matches[4]; 
		$mailTo = '#MAIL:' . strrev(str_replace('mailto:', '', $mailTo));
		
		return $matches[1] . $matches[2] . $mailTo . $matches[5];
	}
}
