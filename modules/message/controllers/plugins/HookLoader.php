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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-04-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_Controllers_Plugins_HookLoader extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Show the private message menu in the back-end
		// I have to register the hook in Plugin level instead of the module bootstrap,
		// because it is not possible to get the view helper instance in the module bootstrap
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$view	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$helper = $view->helperLoader('message')->message();
			
			Core_Base_Hook_Registry::getInstance()
				->register('Core_Layout_Admin_ShowPersonalMenu', array($helper, 'showUnreadMessages'));
		}
	}
}
