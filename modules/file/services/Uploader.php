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
 * @subpackage	services
 * @since		1.0
 * @version		2012-04-20
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Services_Uploader
{
	/**
	 * Consists of the supported image extensions, separated by a comma
	 * 
	 * @var array
	 */
	public static $IMAGE_EXTS = array('jpeg', 'jpg', 'gif', 'png');
	
	/**
	 * Upload errors
	 */
	const ERROR_NOT_UPLOADABLE_EXT = 'ERROR_NOT_UPLOADABLE_EXT';
	const ERROR_BIG_FILE_SIZE	   = 'ERROR_BIG_FILE_SIZE';
	const ERROR_GENERAL			   = 'ERROR_GENERAL';
	
	/**
	 * Uploads a file or multiple files
	 * 
	 * @param string $fileName The file input element that I can retire the uploaded files via $_FILES
	 * @param string $module The module that user are uploading files from.
	 * It will be used to define the upload directory
	 * @param string $options The uploading options, can contain the following options:
	 * - thumbnail: TRUE or FALSE. If TRUE, it will generate various thumbnails
	 * - watermark: TRUE or FALSE. If TRUE, it will add watermark to thumbnails
	 * @return array The array of uploaded file. Each array item consists of the following keys:
	 * - original, and the thumbnails name such as square, small, medium, etc. 
	 * 
	 * The value of key is an array, which has the following members:
	 * - url: The URL of file
	 * - name: The file's name
	 * - extension: File's extension
	 * - width: Width (in pixels) if the uploaded file is an image
	 * - height: Height (in pixels) if the uploaded file is an image
	 * - size: Size of uploaded file (in bytes)
	 */
	public static function upload($fileName, $module = 'file', $options = array())
	{
		if (!isset($_FILES[$fileName])) {
			return array(
				array(
					'original' => array(
						'name'		=> '',
						'extension' => '',
						'size'		=> 0,
						'error'		=> self::ERROR_GENERAL,
					),
				),
			);
		}
		
		Core_Services_Db::connect('master');
		
		// Get the list of uploadable extensions
		$uploadableTypes = Core_Services_Config::get('file', 'uploadable_files', 'bmp,gif,jpeg,jpg,png,txt,zip');
		$uploadableTypes = strtolower($uploadableTypes);
		$uploadableTypes = explode(',', $uploadableTypes);
		
		$files	   = array();
		
		// Prepare the directories to store the uploaded files
		$user	   = Zend_Auth::getInstance()->getIdentity();
		$uploadDir = self::getUploadDir($user, $module);
		$prefixUrl = rtrim('/' . str_replace(DS, '/', ltrim($uploadDir, '/')), '/');
		
		Core_Base_File::createDirectories($uploadDir, APP_ROOT_DIR);
		
		// Upload files
		$numFiles   = count($_FILES[$fileName]['name']);
		$toolkit    = (Core_Services_Config::get('file', 'image_toolkit', File_Services_Installer::DEFAULT_IMAGE_TOOLKIT) == File_Services_Installer::DEFAULT_IMAGE_TOOLKIT)
						? new Core_Base_Image_Adapters_Gd()
						: new Core_Base_Image_Adapters_Imagick();
		$thumbnails = Core_Services_Config::get('file', 'image_thumbnails', File_Services_Installer::DEFAULT_IMAGE_THUMBNAILS);
		$thumbnails = Zend_Json::decode($thumbnails);
		
		for ($i = 0; $i < $numFiles; $i++) {
			$extension		= explode('.', $_FILES[$fileName]['name'][$i]);
			$extension		= strtolower($extension[count($extension) - 1]);
			$name			= basename($_FILES[$fileName]['name'][$i], '.' . $extension);
			
			if (!in_array($extension, $uploadableTypes)) {
				$files[] = array(
					'original' => array(
						'name'		=> $name,
						'extension' => $extension,
						'size'		=> 0,
						'error'		=> self::ERROR_NOT_UPLOADABLE_EXT,
					),
				);
				continue;
			}
			
			$fileId			= Core_Base_String::clean($name, '-') . '-' . uniqid();
			$uploadFileName = $fileId . '.' . $extension;
			$uploadFilePath = APP_ROOT_DIR . DS . $uploadDir . DS . $uploadFileName;
			
			// Move uploaded file to the target directory
			if (move_uploaded_file($_FILES[$fileName]['tmp_name'][$i], $uploadFilePath) === false) {
				$files[] = array(
					'original' => array(
						'name'		=> $name,
						'extension' => $extension,
						'size'		=> 0,
						'error'		=> self::ERROR_GENERAL,
					),
				);
				continue;
			}
			
			$item = array(
				'original' => array(
					'url'		=> $prefixUrl . '/' .  $uploadFileName,
					'name'		=> $name,
					'size'		=> filesize($uploadFilePath),
					'extension' => $extension,
				),
			);
			
			if (in_array($extension, self::$IMAGE_EXTS)) {
				$toolkit->setSourceImage($uploadFilePath);
				$item['original']['height'] = $toolkit->getWidth();
				$item['original']['width']  = $toolkit->getHeight();
				
				// Get the watermark options
				$watermark = array(
					'using'		 => 'none',
					'thumbnails' => array(),
				);
				if (isset($options['watermark']) && ($options['watermark'] == true)
					&& ($watermarkOptions = Core_Services_Config::get('file', 'watermark', null)))
				{
					$watermark				 = Zend_Json::decode($watermarkOptions);
					$watermark['font']		 = str_replace('/', DS, $watermark['font']);
					$watermark['font']		 = APP_ROOT_DIR . DS . ltrim($watermark['font'], DS);
					$watermark['image']		 = str_replace('/', DS, $watermark['image']);
					$watermark['image']		 = APP_ROOT_DIR . DS . ltrim($watermark['image'], DS);
					$watermark['thumbnails'] = explode(',', $watermark['thumbnails']);
				}
				
				// Generate thumbnails if they are requested
				if (isset($options['thumbnail']) && $options['thumbnail'] == true) {
					foreach ($thumbnails as $key => $value) {
						list($method, $width, $height) = explode('|', $value);
						$newFileName = $fileId . '_' . $key . '.' . $extension;
						$newFilePath = APP_ROOT_DIR . DS . $uploadDir . DS . $newFileName;
						
						// Set the destination image
						$toolkit->setSourceImage($uploadFilePath)
								->setDestinationImage($newFilePath);
						switch ($method) {
							case 'resize':
								$toolkit->fit($width, $height);
								break;
							case 'crop':
								$toolkit->crop($width, $height);
								break;
						}
						
						if (in_array($key, $watermark['thumbnails'])) {
							$toolkit->setSourceImage($newFilePath)
									->setDestinationImage($newFilePath);
							switch ($watermark['using']) {
								case 'text':
									$toolkit->addTextWatermark($watermark['text'], $watermark['horizontal_pos'], $watermark['vertical_pos'], $watermark);
									break;
								case 'image':
									$toolkit->addImageWatermark($watermark['image'], $watermark['horizontal_pos'], $watermark['vertical_pos']);
									break;
								case 'none':
									break;
							}
						}
						
						$item[$key] = array(
							'url'		=> $prefixUrl . '/' . $newFileName,
							'name'		=> $name,
							'size'		=> filesize($newFilePath),
							// FIXME: Calculate the width and height of thumbnails
							'height'	=> $height,
							'width'		=> $width,
							'extension' => $extension,
						);
					}
				}
				
				// Add watermark to the original image if enabled
				$toolkit->setSourceImage($uploadFilePath)
						->setDestinationImage($uploadFilePath);
				switch ($watermark['using']) {
					case 'text':
						$toolkit->addTextWatermark($watermark['text'], $watermark['horizontal_pos'], $watermark['vertical_pos'], $watermark);
						break;
					case 'image':
						$toolkit->addImageWatermark($watermark['image'], $watermark['horizontal_pos'], $watermark['vertical_pos']);
						break;
					case 'none':
						break;
				}
			}
			
			$files[] = $item;
		}
		
		return $files;
	}
	
	/**
	 * Gets the upload dir of given user
	 * 
	 * @param Core_Models_User $user The user instance
	 * @param string $module The module's name
	 * @return string
	 */
	public static function getUploadDir($user, $module)
	{
		$search	   = array('/', '{#module#}', '{#user_name#}', '{#user_id#}', '{#year#}', '{#month#}');
		$replace   = array(DS, $module, $user->user_name, $user->user_id, date('Y'), date('m'));
		$uploadDir = Core_Services_Config::get('file', 'upload_dir_template', File_Services_Installer::DEFAULT_UPLOAD_DIR_TEMPLATE);
		$uploadDir = str_replace($search, $replace, $uploadDir);
		$uploadDir = ltrim(rtrim($uploadDir, DS), DS);
		return $uploadDir;
	}
}
