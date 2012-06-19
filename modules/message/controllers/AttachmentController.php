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

class Message_AttachmentController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Deletes attachment
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$request = $this->getRequest();
		$path	 = $request->getParam('path');
		$user    = Zend_Auth::getInstance()->getIdentity();
		$result  = Message_Services_Attachment::delete($path, $user);
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Downloads attachment
	 * 
	 * @return void
	 */
	public function downloadAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$path	 = $request->getParam('path');
		$path	 = Core_Services_Encryptor::decrypt($path);
		$user    = Zend_Auth::getInstance()->getIdentity();
		$file    = Message_Services_Attachment::download($path, $user);
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
		}
		exit();
	}
	
	/**
	 * Uploads attachments
	 * 
	 * @return void
	 */
	public function uploadAction()
	{
		$request   = $this->getRequest();
		$fileName  = $request->getParam('name');
		
		// FIXME: How to set it in the client side when using Dojo Uploader?
		$fileName  = 'uploadedfiles';
		$user	   = Zend_Auth::getInstance()->getIdentity();
		$files     = Message_Services_Attachment::upload($fileName, $user);
		$this->_helper->json($files);
	}
}
