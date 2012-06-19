<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Hooks_Editor_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_media', array($this, 'menu'), true);
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/modules/media/css/admin/default/styles.css')
								->appendStylesheet($this->view->APP_STATIC_URL . '/modules/media/hooks/editor/styles.css')
								->appendStylesheet($this->view->APP_STATIC_URL . '/static/js/dojo/' . APP_DOJO_VER . '/dojox/layout/resources/ResizeHandle.css');
		}
	}
	
	/**
	 * @return void
	 */
	public function menuAction()
	{
	}
	
	/**
	 * @return void
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		$this->view->assign('module', $request->getModuleName());
	}
}
