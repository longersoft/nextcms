<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_Widgets_Banners_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		$this->view->assign(array(
			'zones' => Ad_Services_Zone::find(),
		));
	}
	
	/**
	 * Shows the banners
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		$request = $this->getRequest();
		$zoneId  = $request->getParam('zone_id');
		if ($zoneId && $zone = Ad_Services_Zone::getById($zoneId)) {
			$page   = new Core_Models_Page(array(
				'template' => Core_Services_Template::getCurrentTemplate(),
				'route'	   => Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName(),
				'language' => Zend_Controller_Front::getInstance()->getRequest()->getParam('lang'),
			));
			$result  = Ad_Services_Banner::getBannersInZone($zone, $page);
			$banners = array();
			if ($result) {
				foreach ($result as $row) {
					$banners[] = $row->getProperties(array('title', 'format', 'code', 'target', 'target_url', 'url', 'banner_url', 'page_url'));
				}
			}
			
			$this->view->assign(array(
				'zone'	  => $zone,
				'banners' => $banners,
			));
		}
	}
}
