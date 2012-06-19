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
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-11-04
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_ConfigController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Configures the module
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				if ($apiKey = $request->getPost('flickr_api_key')) {
					Core_Services_Config::set('media', 'flickr_api_key', $apiKey);
				}
				if ($secretKey = $request->getPost('flickr_secret_key')) {
					Core_Services_Config::set('media', 'flickr_secret_key', $secretKey);
				}
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				break;
		}
	}
}
