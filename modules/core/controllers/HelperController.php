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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-05-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_HelperController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////

	/**
	 * Embeds a video
	 * 
	 * @return void
	 */
	public function playAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
	}
	
	////////// BACKEND ACTIONS //////////	
	
	/**
	 * Generates slug
	 * 
	 * @return void
	 */
	public function slugAction()
	{
		Core_Services_Db::connect('slave');
		
		$request = $this->getRequest();
		$string  = $request->getParam('input');
		$locale  = $request->getParam('locale');
		if ($locale == null || $locale == '') {
			$locale = Core_Services_Config::get('core', 'language_code', 'en_US');
		}
		$output  = array(
			'output' => Core_Base_String::clean($string, '-', $locale),
		);
		$this->_helper->json($output);
	}
}
