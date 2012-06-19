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

class Core_View_Helper_UrlHelper extends Zend_View_Helper_Abstract
{
	/**
	 * Gets the helper instance
	 * 
	 * @return Core_View_Helper_UrlHelper
	 */
	public function urlHelper()
	{
		return $this;
	}
	
	/**
	 * Normalizes a given URL
	 * 
	 * @param string $url
	 * @return string
	 */
	public function normalizeUrl($url)
	{
		switch (true) {
			case ('' == $url):
			case ('#' == $url):
			case (('http://' == substr($url, 0, 7)) || ('https://' == substr($url, 0, 8))):
			case ('javascript:' == substr($url, 0, 11)):
				return $url;
			default:
				return rtrim($this->view->APP_URL) . '/' . ltrim($url, '/');
		}
	}
	
	/**
	 * Normalizes a given file URL. It will add APP_ROOT_URL at the begining
	 * of the URL.
	 * 
	 * @param string $url
	 * @return string
	 */
	public function normalizeFileUrl($url)
	{
		switch (true) {
			case (('http://' == substr($url, 0, 7)) || ('https://' == substr($url, 0, 8))):
				return $url;
			default:
				return rtrim($this->view->APP_ROOT_URL) . '/' . ltrim($url, '/');
		}
	}
}
