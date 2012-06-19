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
 * @version		2012-04-20
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_DownloadController extends Zend_Controller_Action
{
	/**
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();		
	}
	
	////////// FRONTEND ACTIONS //////////	
	
	/**
	 * Downloads a photo
	 * 
	 * @return void
	 */
	public function photoAction()
	{
		$this->view->headTitle()->append($this->view->translator()->_('download.photo.title'));
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$photoId = $request->getParam('photo_id');
		$slug	 = $request->getParam('slug');
		$size	 = $request->getParam('size', 'original');
		
		if ($photoId) {
			$photo = Media_Services_Photo::getById($photoId);
		} elseif ($slug) {
			$result = Media_Services_Photo::find(array(
				'slug'	 => $slug,
				'status' => Media_Models_Photo::STATUS_ACTIVATED,
			), 0, 1);
			$photo = ($result && count($result) > 0) ? $result[0] : null;
		}
		
		if ($photo == null || $photo->status != Media_Models_Photo::STATUS_ACTIVATED) {
			throw new Core_Base_Exception_NotFound('Cannot find the photo');
		}
		
		// Increase the number of downloads
		Core_Services_Counter::register($photo, 'downloads', 'Media_Services_Photo::increaseNumDownloads', array($photo));
		
		$file	 = APP_ROOT_DIR . $photo->__get('image_' . $size);
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
}
