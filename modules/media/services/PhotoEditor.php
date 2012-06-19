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
 * @subpackage	services
 * @since		1.0
 * @version		2011-12-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * This class provides the methods to process photo
 */
class Media_Services_PhotoEditor
{
	/**
	 * The user that is going to edit the image
	 * 
	 * @var Core_Models_User
	 */
	private $_user;
	
	/**
	 * The thumbnail size (square, thumbnail, small, crop, medium, large)
	 * 
	 * @var string
	 */
	private $_size;
	
	/**
	 * @var Core_Base_Image_Abstract
	 */
	private $_toolkit;
	
	/**
	 * Path to the source file
	 * 
	 * @var string
	 */
	private $_sourceFile;
	
	/**
	 * Path of the original file
	 * 
	 * @var string
	 */
	private $_originalFile;
	
	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->_toolkit = (Core_Services_Config::get('file', 'image_toolkit', 'gd') == 'gd')
						? new Core_Base_Image_Adapters_Gd()
						: new Core_Base_Image_Adapters_Imagick();
	}
	
	/**
	 * @param Core_Models_User $user
	 * @return Media_Services_PhotoEditor
	 */
	public function setUser($user)
	{
		$this->_user = $user;
		return $this;
	}
	
	/**
	 * @param string $size
	 * @return Media_Services_PhotoEditor
	 */
	public function setThumbnailSize($size)
	{
		$this->_size = $size;
		return $this;
	}
	
	/**
	 * @param string $file
	 * @return Media_Services_PhotoEditor
	 */
	public function setOriginalFile($file)
	{
		$this->_originalFile = $file;
		return $this;
	}
	
	/**
	 * @param string $sourceFile
	 * @return Media_Services_PhotoEditor
	 */
	public function setSourceFile($file)
	{
		$this->_sourceFile = $file;
		return $this;
	}
	
	////////// PROCESSING METHODS //////////
	
	/**
	 * Rotates the image
	 * 
	 * @param float $angle
	 * @return string The URL of generated image 
	 */
	public function rotate($angle)
	{
		return $this->_process('rotate', array('angle' => $angle));
	}
	
	/**
	 * Flips/flops the image
	 * 
	 * @param string $direction Can be "horizontal" or "vertical"
	 * @return string The URL of generated image
	 */
	public function flip($direction)
	{
		return $this->_process('flip', array('direction' => $direction));
	}
	
	/**
	 * Crops the image
	 * 
	 * @param int $width
	 * @param int $height
	 * @param int $top
	 * @param int $left
	 * @return string The URL of generated image
	 */
	public function crop($width, $height, $top, $left)
	{
		return $this->_process('crop', array('width' => $width, 'height' => $height, 'top' => $top, 'left' => $left));
	}
	
	/**
	 * Resizes the image
	 * 
	 * @param int $width
	 * @param int $height
	 * @return string The URL of generated image
	 */
	public function resize($width, $height)
	{
		return $this->_process('resize', array('width' => $width, 'height' => $height));
	}
	
	/**
	 * Completes the processing
	 * 
	 * @return string The URL of thumbnail
	 */
	public function complete()
	{
		if ($this->_sourceFile == $this->_originalFile) {
			return $this->_originalFile;
		}
		
		$source = APP_ROOT_DIR . '/' . ltrim($this->_sourceFile, '/');
		$des	= APP_ROOT_DIR . '/' . ltrim($this->_originalFile, '/');
		copy($source, $des);
		
		return $this->_originalFile;
	}
	
	/**
	 * Deletes the temp files generated when processing the image
	 * 
	 * @return void
	 */
	public function clean()
	{
		Core_Base_File::deleteDirectory(APP_ROOT_DIR . '/' . $this->_getTempDir());
	}
	
	/**
	 * Generates the image
	 * 
	 * @param string $method The method that is used to process image
	 * @param array $params The parameters that will be passed to the processing method.
	 * @return string The new URL of thumbnail
	 */
	private function _process($method, $params)
	{
		$this->_toolkit->setSourceImage(APP_ROOT_DIR . '/' . ltrim($this->_sourceFile, '/'));
		
		// Determine the temp file
		$tempDir = $this->_getTempDir();
		Core_Base_File::createDirectories($tempDir, APP_ROOT_DIR);
		
		$extension = explode('.', $this->_originalFile);
		$extension = strtolower($extension[count($extension) - 1]);
		$tempPath  = $tempDir . '/' . uniqid() . '.' . $extension; 
		$tempFile  = APP_ROOT_DIR . '/' . ltrim($tempPath, '/');
		
		$this->_toolkit->setDestinationImage($tempFile);
		switch ($method) {
			case 'rotate':
				$this->_toolkit->rotate($params['angle']);
				break;
			case 'flip':
				$this->_toolkit->flip($params['direction']);
				break;
			case 'crop':
				$this->_toolkit->crop($params['width'], $params['height'], $params['left'], $params['top'], false);
				break;
			case 'resize':
				$this->_toolkit->resize($params['width'], $params['height']);
				break;
		}
		
		return str_replace(DS, '/', $tempPath);
	}
	
	/**
	 * Gets the path of directory that is used to contain the temp images
	 * 
	 * @return string
	 */
	private function _getTempDir()
	{
		return '/temp/media_photo_' . $this->_user->user_id . '_' . $this->_size;
	}
}
