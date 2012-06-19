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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Hooks_Linkprovider_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_content', array($this, 'menu'), true);
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/modules/content/hooks/linkprovider/styles.css');
		}
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
	
	/**
	 * Shows categories
	 * 
	 * @return void
	 */
	public function categoryAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language');
		$this->view->assign(array(
			'type'		 => $request->getParam('type', Content_Models_Article::TYPE_ARTICLE),
			'categories' => Category_Services_Category::getTree('content', $language),
		));
	}
	
	/**
	 * Shows RSS/Atom channels
	 * 
	 * @return void
	 */
	public function feedAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language');
		
		$this->view->assign(array(
			'type'		 => $request->getParam('type', Content_Models_Article::TYPE_ARTICLE),
			'feedFormat' => $request->getParam('feed_format', 'rss'),
			'categories' => Category_Services_Category::getTree('content', $language),
		));
	}
}
