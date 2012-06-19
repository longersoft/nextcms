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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-04-24
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Widgets_Breadcrumb_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows categories
	 * 
	 * @return void
	 */
	public function categoryAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$language	= $request->getParam('language');
		$categories = Category_Services_Category::getTree('content', $language);
		
		$array = array();
		if ($categories) {
			foreach ($categories as $category) {
				$array[] = array(
					'category_id' => $category->category_id,
					'label'		  => str_repeat('---', $category->depth) . $category->name,
				);
			}
		}
		echo Zend_Json::encode($array);
	}
	
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
			'language'	 => $language,
			'languages'  => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
			'categories' => Category_Services_Category::getTree('content', $language),
			'uid'		 => uniqid(),
		));
	}
	
	/**
	 * Shows the breadcrumb
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request    = $this->getRequest();
		$categoryId = $request->getParam('category_id');
		$category   = $categoryId ? Category_Services_Category::getById($categoryId) : null;
		$categories = $category ? Category_Services_Category::getParents($category) : null;
		
		$this->view->assign(array(
			'category'   => $category,
			'categories' => $categories,
			'type'		 => $request->getParam('type', Content_Models_Article::TYPE_ARTICLE),
		));
	}
}
