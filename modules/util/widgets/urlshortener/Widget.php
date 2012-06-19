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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-04-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Widgets_Urlshortener_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Shows the short URL
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request  = $this->getRequest();
		$url	  = $this->view->serverUrl() . $request->getRequestUri();
		$service  = $request->getParam('service', 'TinyUrlCom');
		$custom   = $request->getParam('custom_service');
		$obj	  = null;
		if (!$custom || !class_exists($custom) || !(($obj = new $custom()) instanceof Zend_Service_ShortUrl_AbstractShortener)) {
			$service = 'Zend_Service_ShortUrl_' . $service;
			$obj	 = new $service();
		} 
		$url = $obj->shorten($url);
		$this->view->assign('url', $url);
	}
}
