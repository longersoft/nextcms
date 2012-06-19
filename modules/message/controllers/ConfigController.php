<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_ConfigController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Configures the Message module
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
				// Private messages settings
				Core_Services_Config::set('message', 'attachments_dir', $request->getPost('attachments_dir', Message_Services_Installer::DEFAULT_ATTACHMENTS_DIR));
				Core_Services_Config::set('message', 'attachments_exts', $request->getPost('attachments_exts'));
				Core_Services_Config::set('message', 'email_enabled', $request->getPost('email_enabled') ? 'true' : 'false');
				Core_Services_Config::set('message', 'message_sent_template', Zend_Json::encode(array(
					'from_name'  => $request->getPost('sent_from_name'),
					'from_email' => $request->getPost('sent_from_email'),
					'subject'    => $request->getPost('sent_subject'),
					'content'    => $request->getPost('sent_content'),
				)));
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'messageSentMailTemplate' => Core_Services_Mail::getBuiltinTemplate('message', 'message_sent_template'),
				));
				break;
		}
	}
}
