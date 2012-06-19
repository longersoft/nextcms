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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Widgets_Login_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the login box
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$this->view->assign(array(
			'user'			=> $user,
			'returnUrl'		=> $this->getRequest()->getRequestUri(),
			'openIdEnabled' => Core_Services_Config::get('core', 'register_openid_enabled', 'false') == 'true',
		));
	}
}
