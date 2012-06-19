<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-13
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Hooks_Attachmentprovider_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_file', array($this, 'menu'), true);
	}
	
	/**
	 * Shows the menu in the back-end
	 * 
	 * @return void
	 */
	public function menuAction()
	{
	}
	
	/**
	 * Shows the toolbox
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('master');
		
		$this->view->assign(array(
			'language'  => Core_Services_Config::get('core', 'localization_default_language', 'en_US'),
			'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
		));
	}
}
