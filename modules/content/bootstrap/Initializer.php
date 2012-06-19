<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	bootstrap
 * @since		1.0
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Bootstrap_Initializer extends Core_Base_Application_Module_Initializer
{
	/**
	 * Registers internal hooks
	 * 
	 * @return void
	 */
	protected function _initHooks()
	{
		// Update the sitemap after activating an article
//		Core_Base_Hook_Registry::getInstance()->register('Content_Activate_Article', 'Content_Services_Sitemap::update');
	}
}
