<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	bootstrap
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Bootstrap extends Core_Base_Application_Module_Bootstrap
{
	/**
	 * Registers plugins
	 * 
	 * @return void
	 */
	protected function _initPlugins()
	{
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new Message_Controllers_Plugins_HookLoader());
	}
}
