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

class Core_View_Helper_BackendUrl
{
	/**
	 * Gets base URL of the back-end, such as /admin/, /yourAlias/index.php/admin
	 * 
	 * @return string
	 */
	public function backendUrl()
	{
		Core_Services_Db::connect('master');
		
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		return rtrim($baseUrl, '/') . '/' . Core_Services_Config::get('core', 'admin_prefix', 'admin');
	}
}
