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
 * @version		2012-05-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_Avatar extends Zend_View_Helper_Abstract
{
	/**
	 * @var string
	 */
	private static $_rootUrl = null;
	
	/**
	 * @var string
	 */
	private static $_defaultAvatar = null;
	
	/**
	 * @var bool
	 */
	private static $_useGravatar = false;
	
	/**
	 * @var bool
	 */
	private static $_init = false;
	
	/**
	 * Renders the img tag that shows user's avatar
	 * 
	 * @param Core_Models_User $user
	 * @param int $size Image size
	 * @return string
	 */
	public function avatar($user, $size = 80)
	{
		if (!self::$_init) {
			Core_Services_Db::connect('master');
			
			self::$_init = true;
			self::$_rootUrl		  = $this->view->serverUrl() . $this->view->baseUrl();
			self::$_defaultAvatar = Core_Services_Config::get('core', 'default_avatar', '/modules/core/images/defaultUserAvatar.png');
			self::$_useGravatar	  = Core_Services_Config::get('core', 'use_gravatar', false);
		}
		
		switch (true) {
			case ($user->avatar && !empty($user->avatar)):
				return ('http://' == substr($user->avatar, 0, 7) || 'https://' == substr($user->avatar, 0, 8))
						? '<img src="' . $user->avatar . '" />'
						: '<img src="' . self::$_rootUrl . '/' . ltrim($user->avatar, '/') . '" />';
			case (self::$_useGravatar):
				return $this->view->gravatar($user->email, array('img_size' => $size));
			case ($user->avatar == null):
				return '<img src="' . self::$_rootUrl . '/' . ltrim(self::$_defaultAvatar, '/') . '" />';
		}
	}
}
