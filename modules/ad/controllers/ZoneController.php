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
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_ZoneController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Adds new zone
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$zone    = new Ad_Models_Zone(array(
			'name'	 => $request->getPost('name'),
			'width'  => $request->getPost('width'),
			'height' => $request->getPost('height'),
		));
		$result = Ad_Services_Zone::add($zone);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Deletes zone
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$zoneId  = $request->getParam('zone_id');
		$zone    = Ad_Services_Zone::getById($zoneId);
		$result  = Ad_Services_Zone::delete($zone);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Updates zone
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$zoneId  = $request->getParam('zone_id');
		$zone    = Ad_Services_Zone::getById($zoneId);
		$result  = false;
		if ($zone) {
			$zone->name   = $request->getPost('name');
			$zone->width  = $request->getPost('width');
			$zone->height = $request->getPost('height');
			$result = Ad_Services_Zone::update($zone);
		}
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Lists zones
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		switch ($format) {
			case 'html':
				$zones = Ad_Services_Zone::find();
				$this->view->assign('zones', $zones);
				break;
			default:
				break;
		}
	}
}
