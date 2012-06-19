<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-12-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_FileController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Uploads files
	 * 
	 * @return void
	 */
	public function uploadAction()
	{
		$request   = $this->getRequest();
		$fileName  = $request->getParam('name');
		$module    = $request->getParam('mod', 'file');
		$thumbnail = $request->getParam('thumbnail', 'false');
		$watermark = $request->getParam('watermark', 'false');
		
		// I use Dojo Uploader in the client side, and 'uploadedfiles' is the default name.
		// FIXME: How to set it?
		$fileName  = 'uploadedfiles';
		$files	   = File_Services_Uploader::upload($fileName, $module, array(
			'thumbnail' => $thumbnail == 'true',
			'watermark' => $watermark == 'true',
		 ));
		
		$this->_helper->json($files);
	}
}
