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
 * @version		2012-05-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Widgets_Archive_Widget extends Core_Base_Extension_Widget
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
	 * Shows all the dates and number of articles activated in each date
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$type	  = $request->getParam('type', Content_Models_Article::TYPE_ARTICLE);
		$criteria = array(
			'category_id' => $request->getParam('category_id'),
			'language'	  => $request->getParam('language'),
			'status'	  => Content_Models_Article::STATUS_ACTIVATED,
			'type'		  => $type,
		);
		if ('__AUTO__' == $criteria['category_id']) {
			$criteria['category_id'] = null;
		}
		$archives	  = Content_Services_Article::countByDate($criteria);
		$posts		  = array();
		$numDays	  = date('t');
		$currentYear  = date('Y');
		$currentMonth = date('m');
		for ($i = 1; $i <= $numDays; $i++) {
			$day  = $i < 10 ? '0' . $i : $i;
			$date = $currentYear . '-' . $currentMonth . '-' . $day;
			$posts[$i . ''] = array(
				'dayInWeek' => date('w', strtotime($date)),
				'numPosts'  => isset($archives[$date]) ? $archives[$date] : 0,
			);
		}
		
		$this->view->assign(array(
			'title'		   => $request->getParam('title', ''),
			'type'		   => $type,
			'posts'		   => $posts,
			'category'	   => $criteria['category_id'] ? Category_Services_Category::getById($criteria['category_id']) : null,
			'currentDay'   => date('d'),
			'currentMonth' => $currentYear . '-' . $currentMonth,
		));
	}
}
