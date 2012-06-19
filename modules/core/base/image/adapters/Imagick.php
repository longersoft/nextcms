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
 * @version		2011-12-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Image_Adapters_Imagick extends Core_Base_Image_Abstract
{
	/**
	 * @see Core_Base_Image_Abstract::addImageWatermark()
	 */
	public function addImageWatermark($watermarkImage, $horizontalPos = 'top', $verticalPos = 'left')
	{
		$size = @getimagesize($watermarkImage);
		$w	  = $size[0];
		$h	  = $size[1];
		if ($this->_width < $w + $this->_watermarkPadding || $this->_height < $h + $this->_watermarkPadding) {
			return false;
		}
		
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
		
		$watermark = new Imagick();
		$watermark->readImage($watermarkImage);
		
		$imagick = new Imagick();
		$imagick->readImage($this->_sourceImage);
		$imagick->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);
	    $imagick->writeImage($this->_destinationImage);
	    
	    $watermark->clear();
		$watermark->destroy();
	    $imagick->clear();
		$imagick->destroy();
		
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
			$imageDraw = new ImagickDraw();
			$imageDraw->setFontSize(12);
			$imageDraw->setFont($options['font']);
			
			$imagick = new Imagick();
			$metrics = $imagick->queryFontMetrics($imageDraw, $watermarkText, false);
			$size    = (int) ($this->_width / 2) * 12 / $metrics['textWidth'];
			
			$imagick->clear();
			$imagick->destroy();
			$imageDraw->clear();
			$imageDraw->destroy();
		} else {
			$size = $options['font_size'];
		}
		
		$imagick = new Imagick();
		$imagick->readImage($this->_sourceImage);
		
		$imageDraw = new ImagickDraw();	
		switch (true) {
			case ($horizontalPos == 'left' && $verticalPos == 'top'):
				$imageDraw->setgravity(Imagick::GRAVITY_NORTHWEST);
				break;
			case ($horizontalPos == 'left' && $verticalPos == 'middle'):
				$imageDraw->setgravity(Imagick::GRAVITY_WEST);
				break;
			case ($horizontalPos == 'left' && $verticalPos == 'bottom'):
				$imageDraw->setgravity(Imagick::GRAVITY_SOUTHWEST);
				break;
			case ($horizontalPos == 'center' && $verticalPos == 'top'):
				$imageDraw->setgravity(Imagick::GRAVITY_NORTH);
				break;
			case ($horizontalPos == 'center' && $verticalPos == 'middle'):
				$imageDraw->setgravity(Imagick::GRAVITY_CENTER);
				break;
			case ($horizontalPos == 'center' && $verticalPos == 'bottom'):
				$imageDraw->setgravity(Imagick::GRAVITY_SOUTH);
				break;
			case ($horizontalPos == 'right' && $verticalPos == 'top'):
				$imageDraw->setgravity(Imagick::GRAVITY_NORTHEAST);
				break;
			case ($horizontalPos == 'right' && $verticalPos == 'middle'):
				$imageDraw->setgravity(Imagick::GRAVITY_EAST);
				break;
			case ($horizontalPos == 'right' && $verticalPos == 'bottom'):
				$imageDraw->setgravity(Imagick::GRAVITY_SOUTHEAST);
				break;
		}
	  
		$imageDraw->setFont($options['font']);
		$imageDraw->setFontSize($size);
		$imageDraw->setFillOpacity($options['opacity']);
		$imageDraw->setFillColor($options['color']);
		
		$imagick->annotateImage($imageDraw, 5, 5, $options['rotation_angle'], $watermarkText);
		$imagick->writeImage($this->_destinationImage);
	  
		$imagick->clear();
		$imagick->destroy();
		$imageDraw->clear();
		$imageDraw->destroy();
		
		return true;
	}
	
	/**
	 * @see Core_Base_Image_Abstract::resize()
	 */
	public function resize($w, $h)
	{
		$imagick = new Imagick();
		$imagick->readImage($this->_sourceImage);

		$imagick->resizeImage($w, $h, Imagick::FILTER_LANCZOS, 1);
		$imagick->writeImage($this->_destinationImage);
		
		$imagick->clear();
		$imagick->destroy();
	}
	
	/**
	 * @see Core_Base_Image_Abstract::rotate()
	 */
	public function rotate($angle)
	{
		$imagick = new Imagick();
		$imagick->readImage($this->_sourceImage);

		$imagick->rotateImage(new ImagickPixel('#ffffff'), $angle);
//		$imagick->rotateImage(new ImagickPixel('transparent'), $angle);
		$imagick->writeImage($this->_destinationImage);
		
		$imagick->clear();
		$imagick->destroy();
	}
	
	/**
	 * @see Core_Base_Image_Abstract::flip()
	 */
	public function flip($direction)
	{
		$imagick = new Imagick();
		$imagick->readImage($this->_sourceImage);

		switch ($direction) {
			case Core_Base_Image_Abstract::FLIP_VERTICAL:
				$imagick->flipImage();
				break;
			case Core_Base_Image_Abstract::FLIP_HORIZONTAL:
				$imagick->flopImage();
				break;
		}
		
		$imagick->writeImage($this->_destinationImage);
		$imagick->clear();
		$imagick->destroy();
	}
	
	/**
	 * @see Core_Base_Image_Abstract::_crop()
	 */
	protected function _crop($resizeWidth, $resizeHeight, $w, $h, $cropX, $cropY, $resize = true)
	{
		$imagick = new Imagick();
		$imagick->readImage($this->_sourceImage);

		if ($resize) {
			// Resize first
			$imagick->resizeImage($resizeWidth, $resizeHeight, Imagick::FILTER_LANCZOS, 1);
		}

		// Crop
		$imagick->cropImage($w, $h, $cropX, $cropY);
		$imagick->writeImage($this->_destinationImage);
		
		$imagick->clear();
		$imagick->destroy();
	}
}
