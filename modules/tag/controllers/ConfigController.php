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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-01-13
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_ConfigController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Configures module
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
				Core_Services_Config::set('tag', 'suggestion_enabled', $request->getPost('suggestion_enabled') == 'on' ? 'true' : 'false');
				Core_Services_Config::set('tag', 'opencalais_enabled', $request->getPost('opencalais_enabled') == 'on' ? 'true' : 'false');
				Core_Services_Config::set('tag', 'opencalais_api_key', $request->getPost('opencalais_api_key'));
				Core_Services_Config::set('tag', 'yql_enabled', $request->getPost('yql_enabled') == 'on' ? 'true' : 'false');
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'suggestionsEnabled' => (Core_Services_Config::get('tag', 'suggestion_enabled', 'false') == 'true'),
					'openCalaisEnabled'  => (Core_Services_Config::get('tag', 'opencalais_enabled', 'false') == 'true'),
					'yqlEnabled'		 => (Core_Services_Config::get('tag', 'yql_enabled', 'false') == 'true'),
				));
				break;
		}
	}
}
