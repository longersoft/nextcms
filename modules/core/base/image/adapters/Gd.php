<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	base
 * @since		1.0
 * @version		2012-02-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Image_Adapters_Gd extends Core_Base_Image_Abstract
{
	/**
	 * @see Core_Base_Image_Abstract::addImageWatermark()
	 */
	public function addImageWatermark($watermarkImage, $horizontalPos = 'top', $verticalPos = 'left')
	{
		// Get the size of watermark image
		$size = getimagesize($watermarkImage);
		$w	  = $size[0];
		$h	  = $size[1];
		
		if ($this->_width < $w + $this->_watermarkPadding || $this->_height < $h + $this->_watermarkPadding) {
			return false;
		}
		
		// Calculate the position to place the watermark
		$x = $y = 0;
		switch ($horizontalPos) {
			case 'left':
				$x = 0 + $this->_watermarkPadding;
				break;
			case 'center':
				$x = ($this->_width - $w - $this->_watermarkPadding) / 2;
				break;
			case 'right':
				$x = $this->_width - $w - $this->_watermarkPadding;
				break;
		}
		
		switch ($verticalPos) {
			case 'top':
				$y = 0 + $this->_watermarkPadding;
				break;
			case 'middle':
				$y = ($this->_height - $h - $this->_watermarkPadding) / 2;
				break;
			case 'bottom':
				$y = $this->_height - $h - $this->_watermarkPadding;
				break;
		}
		
		$watermark = $this->_createSourceFile($watermarkImage);
		$source	   = $this->_createSourceFile($this->_sourceImage);
		imagecopy($source, $watermark, $x, $y, 0, 0, $w, $h);
		$this->_createDestinationFile($source, $this->_destinationImage);

		imagedestroy($watermark);
		imagedestroy($source);
		
		return true;
	}
	
	/**
	 * @see Core_Base_Image_Abstract::addTextWatermark()
	 */
	public function addTextWatermark($watermarkText, $horizontalPos = 'top', $verticalPos = 'left', $options = array())
	{
		// Calculate the font size
		$size = 12;
		if (isset($options['font_measured']) && $options['font_measured']) {
			$box  = imagettfbbox(12, 0, $options['font'], $watermarkText);
			$size = (int) ($this->_width / 2) * 12 / $box[2];
		} else {
			$size = $options['font_size'];
		}
		
		$box		 = imagettfbbox($size, 0, $options['font'], $watermarkText);
		$radian		 = deg2rad($options['rotation_angle']);
		$cos		 = cos($radian);
		$sin		 = sin($radian);
		$coordinates = array();
		for ($i = 0; $i < 7; $i += 2) {
			$coordinates[$i + 0] = round($box[$i + 0] * $cos + $box[$i + 1] * $sin);
			$coordinates[$i + 1] = round($box[$i + 1] * $cos - $box[$i + 0] * $sin);
		}
 
		$x0 = min($coordinates[0], $coordinates[2], $coordinates[4], $coordinates[6]) - 5;
		$x1 = max($coordinates[0], $coordinates[2], $coordinates[4], $coordinates[6]) + 5;
		$y0 = min($coordinates[1], $coordinates[3], $coordinates[5], $coordinates[7]) - 5;
		$y1 = max($coordinates[1], $coordinates[3], $coordinates[5], $coordinates[7]) + 5;
		$w  = abs($x1 - $x0);
		$h  = abs($y1 - $y0);
		
		switch ($horizontalPos) {
			case 'left':
				$x = -$x0;
				break;
			case 'center':
				$x = $this->_width / 2 - $w / 2 - $x0;
				break;
			case 'right':
				$x = $this->_width - $x1;
				break;
		}
		
		switch ($verticalPos) {
			case 'top':
				$y = -$y0;
				break;
			case 'middle':
				$y = $this->_height / 2 - $h / 2 - $y0;
				break;
			case 'bottom':
				$y = $this->_height - $y1;
				break;
		}
		
		$red	= hexdec(substr($options['color'], 1, 2));
		$green	= hexdec(substr($options['color'], 3, 2));
		$blue	= hexdec(substr($options['color'], 5, 2));
		$alpha  = 127 * (100 - $options['opacity']) / 100;
		$source = $this->_createSourceFile($this->_sourceImage);
		$color  = imagecolorallocatealpha($source, $red, $green, $blue, $alpha);
		
		imagettftext($source, $size, $options['rotation_angle'], $x, $y, $color, $options['font'], $watermarkText);
		$this->_createDestinationFile($source, $this->_destinationImage);		
		imagedestroy($source);
		
		return true;
	}
	
	/**
	 * @see Core_Base_Image_Abstract::resize()
	 */
	public function resize($w, $h)
	{
		$source	 	 = $this->_createSourceFile($this->_sourceImage);
		$destination = imagecreatetruecolor($w, $h);
		imagecopyresampled($destination, $source, 0, 0, 0, 0, $w, $h, $this->_width, $this->_height);

		$this->_createDestinationFile($destination, $this->_destinationImage);

		imagedestroy($source);
		imagedestroy($destination);
	}
	
	/**
	 * @see Core_Base_Image_Abstract::rotate()
	 */
	public function rotate($angle)
	{
		$source  = $this->_createSourceFile($this->_sourceImage);
		$rotate  = imagerotate($source, 360 - $angle, -1);

		$this->_createDestinationFile($rotate, $this->_destinationImage);

		imagedestroy($source);
		imagedestroy($rotate);
	}
	
	/**
	 * @see Core_Base_Image_Abstract::flip()
	 */
	public function flip($direction)
	{
		$source	   = $this->_createSourceFile($this->_sourceImage);
		$srcX	   = 0;
		$srcY	   = 0;
		$srcWidth  = $this->_width;
		$srcHeight = $this->_height;
		
		switch ($direction){
			case Core_Base_Image_Abstract::FLIP_VERTICAL:
				$srcY	   = $this->_height - 1;
				$srcHeight = -$this->_height;
				break;
			case Core_Base_Image_Abstract::FLIP_HORIZONTAL:
				$srcX	  = $this->_width - 1;
				$srcWidth = -$this->_width;
				break;
		}
		
		$destination = imagecreatetruecolor($this->_width, $this->_height);
		imagecopyresampled($destination, $source, 0, 0, $srcX, $srcY , $this->_width, $this->_height, $srcWidth, $srcHeight);
		
		$this->_createDestinationFile($destination, $this->_destinationImage);
		
		imagedestroy($source);
		imagedestroy($destination);
	}
	
	/**
	 * @see Core_Base_Image_Abstract::_crop()
	 */
	protected function _crop($resizeWidth, $resizeHeight, $w, $h, $cropX, $cropY, $resize = true)
	{
		if ($resize) {
			// Resize
			$this->resize($resizeWidth, $resizeHeight);
			$source = $this->_createSourceFile($this->_destinationImage);
		} else {
			// Crop
			$source = $this->_createSourceFile($this->_sourceImage);
		}
		
		$destination = imagecreatetruecolor($w, $h);
		
//		imagecopyresized($destination, $source, 0, 0, $cropX, $cropY, $w, $h, $resizeWidth, $resizeHeight);
		imagecopy($destination, $source, 0, 0, $cropX, $cropY, $w, $h);

		$this->_createDestinationFile($destination, $this->_destinationImage);

		imagedestroy($source);
		imagedestroy($destination);
	}

	private function _createSourceFile($source) 
	{
		$extension = explode('.', $source);
		$type	   = strtolower($extension[count($extension) - 1]);
		switch ($type) {
			case 'jpg':
			case 'jpeg':
				return imagecreatefromjpeg($source);
				break;
			case 'png':
				return imagecreatefrompng($source);
				break;
			case 'gif':
				return imagecreatefromgif($source);
				break;
			case 'wbmp':
				return imagecreatefromwbmp($source);
				break;
			default:
				throw new Exception('Do not support ' . $type . ' type of image');
				break;
		}
		return null;
	}

	private function _createDestinationFile($source, $destination, $quality = 100)
	{
		switch($this->_sourceImageType) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($source, $destination, $quality);
				break;
			case 'png':
				$quality = ($quality > 9) ? 9 : $quality;
				imagepng($source, $destination, $quality);
				break;
			case 'gif':
				imagegif($source, $destination);
				break;
			case 'wbmp':
				imagewbmp($source, $destination);
				break;
			default:
				throw new Exception('Do not support ' . $this->_sourceImageType . ' type of image');
				break;
		}
	}
}
