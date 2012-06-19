<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_Widgets_Tagcloud_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request    = $this->getRequest();
		$language   = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$this->view->assign(array(
			'language'	=> $language,
			'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
		));
	}
	
	/**
	 * Shows the tag cloud
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request	 = $this->getRequest();
		$language	 = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$routeName	 = $request->getParam('route_name');
		$entityClass = $request->getParam('entity_class');
		
		$cloud = null;
		if ($routeName && $entityClass) {
			$cloud = Tag_Services_Tag::getTagCloud($routeName, $entityClass, $language, $request->getParam('limit', 20));
		}
		$this->view->assign(array(
			'cloud' => $cloud,
			'title' => $request->getParam('title', ''),
		));
	}
}
