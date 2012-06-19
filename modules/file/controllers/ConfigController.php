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
 * @version		2012-05-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_ConfigController extends Zend_Controller_Action
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
				Core_Services_Config::set('file', 'uploadable_files', $request->getPost('uploadable_files', 'bmp,gif,jpeg,jpg,png,txt,zip'));
				Core_Services_Config::set('file', 'image_toolkit', $request->getPost('image_toolkit', File_Services_Installer::DEFAULT_IMAGE_TOOLKIT));
				Core_Services_Config::set('file', 'upload_dir_template', $request->getPost('upload_dir_template', File_Services_Installer::DEFAULT_UPLOAD_DIR_TEMPLATE));
				
				$thumbs  = $request->getPost('thumb');
				$methods = $request->getPost('method');
				$widths  = $request->getPost('width');
				$heights = $request->getPost('height');
				$thumbnails = array();				
				for ($i = 0; $i < count($thumbs); $i++) {
					$thumbnails[$thumbs[$i]] = implode('|', array($methods[$i], $widths[$i], $heights[$i]));
				}
				Core_Services_Config::set('file', 'image_thumbnails', Zend_Json::encode($thumbnails));
				
				// Explorer's settings
				Core_Services_Config::set('file', 'viewable_files', $request->getPost('viewable_files', 'bmp,gif,jpeg,jpg,png,txt'));
				Core_Services_Config::set('file', 'editable_files', $request->getPost('editable_files', 'txt'));
				
				// Watermark settings
				$watermarkOptions = array(
					'using'			 => $request->getPost('watermark_using', 'none'),
					'text'			 => $request->getPost('watermark_text', ''),
					'color'			 => $request->getPost('watermark_color', '#FFF'),
					'font'			 => $request->getPost('watermark_font', File_Services_Installer::DEFAULT_WATERMARK_FONT),
					'font_size'		 => (int) $request->getPost('watermark_font_size', 12),
					'font_measured'  => $request->getPost('watermark_font_measured') ? true : false,
					'rotation_angle' => (int) $request->getPost('watermark_rotation_angle', 0),
					'opacity'		 => (int) $request->getPost('watermark_opacity', 50),
					'image'			 => $request->getPost('watermark_image', ''),
					'horizontal_pos' => $request->getPost('watermark_horizontal_pos', 'top'),
					'vertical_pos'	 => $request->getPost('watermark_vertical_pos', 'left'),
					'thumbnails'	 => implode(',', $request->getPost('watermark_thumbnails', array())),
				);
				Core_Services_Config::set('file', 'watermark', Zend_Json::encode($watermarkOptions));
				
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				// Uploader's settings
				$thumbnails		= Core_Services_Config::get('file', 'image_thumbnails', File_Services_Installer::DEFAULT_IMAGE_THUMBNAILS);
				$thumbnails		= Zend_Json::decode($thumbnails);
				$thumbnailSizes = array();
				foreach ($thumbnails as $key => $value) {
					list($method, $width, $height) = explode('|', $value);
					$thumbnailSizes[$key] = array(
						'method' => $method,
						'width'  => $width,
						'height' => $height,
					);
				}
				
				// Watermark default options
				$watermarkDefaultOptions = array(
					'using'			 => 'none',
					'text'			 => '',
					'color'			 => '#FFF',
					'font'			 => File_Services_Installer::DEFAULT_WATERMARK_FONT,
					'font_size'		 => 12,
					'font_measured'  => false,
					'rotation_angle' => 0,
					'opacity'		 => 50,
					'image'			 => '',
					'horizontal_pos' => 'top',
					'vertical_pos'	 => 'left',
					'thumbnails'	 => '',
				);
				
				$watermarkOptions = Core_Services_Config::get('file', 'watermark');
				$watermarkOptions = $watermarkOptions
								  ? array_merge($watermarkDefaultOptions, Zend_Json::decode($watermarkOptions))
								  : $watermarkDefaultOptions;
				
				$this->view->assign(array(
					'thumbnailSizes'	=> $thumbnailSizes,
					'imageToolkit'		=> Core_Services_Config::get('file', 'image_toolkit', File_Services_Installer::DEFAULT_IMAGE_TOOLKIT),
					'uploadDirTemplate' => Core_Services_Config::get('file', 'upload_dir_template', File_Services_Installer::DEFAULT_UPLOAD_DIR_TEMPLATE),
					'watermarkOptions'  => $watermarkOptions,
				));
				break;
		}
	}
}
