<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-03-16
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_ConfigController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Configures the comment module
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
				Core_Services_Config::set('comment', 'auth_required', $request->getPost('auth_required') ? 'true' : 'false');
				if ($akismetApiKey = $request->getPost('akismet_api_key')) {
					Core_Services_Config::set('comment', 'akismet_api_key', $akismetApiKey);
				} else {
					Core_Services_Config::delete('comment', 'akismet_api_key');
				}
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'akismetApiKey' => Core_Services_Config::get('comment', 'akismet_api_key'),
				));
				break;
		}
	}
}
