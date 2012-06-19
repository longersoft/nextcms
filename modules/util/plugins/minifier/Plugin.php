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

class Util_Plugins_Minifier_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown()
	{
		$layout = Zend_Layout::getMvcInstance();
		if (!$layout || 'admin' == $layout->getLayout()) {
			return;
		}
		
		$response = Zend_Controller_Front::getInstance()->getResponse();
		$body	  = $response->getBody();
		
		// Minify the body and response
		require_once 'Minify/HTML.php';
		$body = Minify_HTML::minify($body);
		$response->setBody($body);
	}
}
