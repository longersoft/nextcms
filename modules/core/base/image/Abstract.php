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

abstract class Core_Base_Image_Abstract
{
	// The flip modes
	const FLIP_VERTICAL   = 'vertical';
	const FLIP_HORIZONTAL = 'horizontal';
	
	/**
	 * The source image
	 * 
	 * @var string
	 */
	protected $_sourceImage;

	/**
	 * Type of source image: gif, jpg, jpeg, png
	 * 
	 * @var string
	 */
	protected $_sourceImageType;

	/**
	 * Width of image
	 * 
	 * @var int
	 */
	protected $_width;

	/**
	 * Height of image
	 * 
	 * @var int
	 */
	protected $_height;
	
	/**
	 * Destination image
	 * 
	 * @var string
	 */
	protected $_destinationImage;
	
	/**
	 * Padding when adding watermark
	 * 
	 * @var int
	 */
	protected $_watermarkPadding = 0;

	/**
	 * Sets the source image
	 * 
	 * @param string $file Path of the source image
	 * @return Core_Base_Image_Abstract
	 */
	public function setSourceImage($file)
	{
		$this->_sourceImage = $file;

		// Get size of image
		$size = getimagesize($this->_sourceImage);

		$this->_width  = $size[0];
		$this->_height = $size[1];
		$extension	   = explode('.', $file);
		$this->_sourceImageType = strtolower($extension[count($extension) - 1]);
		
		return $this;
	}
	
	/**
	 * Sets the destination image
	 * 
	 * @param string $file Path of the destination image
	 * @return Core_Base_Image_Abstract
	 */
	public function setDestinationImage($file)
	{
		$this->_destinationImage = $file;
		return $this;
	}
	
	/**
	 * Gets width of the source image
	 * 
	 * @return int
	 */
	public function getWidth()
	{
		return $this->_width;
	}
	
	/**
	 * Gets height of source image
	 * 
	 * @return int
	 */
	public function getHeight()
	{
		return $this->_height;
	}
	
	/**
	 * Sets the watermark padding
	 * 
	 * @param int $padding
	 * @return Core_Base_Image_Abstract
	 */
	public function setWatermarkPadding($padding = 0)
	{
		$this->_watermarkPadding = $padding;
		return $this;
	}
	
	public function fit($w, $h)
	{
		$percent = ($this->_width > $w) ? (($w * 100) / $this->_width) : 100;
		$w		 = ($this->_width * $percent) / 100;
		$h		 = ($this->_height * $percent) / 100;
		$this->resize($w, $h);
	}

	public function crop($w, $h, $cropX = null, $cropY = null, $resize = true)
	{
		// Maintain ratio if image is smaller than resize
		$percent		  = ($this->_width > $w) ? ($w * 100) / ($this->_width) : 100;

		// Resize to one side to newWidth or newHeight
		$percentWidght	  = ($w * 100) / $this->_width;
		$percentHeight	  = ($h * 100) / $this->_height;
		// FIXME: Why do I re-calculate the percent again here?
		$percent		  = ($percentWidght > $percentHeight) ? $percentWidght : $percentHeight;
		
		if ($percentWidght > $percentHeight) {
			$resizeWidth  = $w;
			$resizeHeight = ($this->_height * $percent) / 100;
		} else {
			$resizeHeight = $h;
			$resizeWidth  = ($this->_width * $percent) / 100;
		}

		$cropX = (null == $cropX) ? ($resizeWidth - $w) / 2 : $cropX;
		$cropY = (null == $cropY) ? ($resizeHeight - $h) / 2 : $cropY;

		$this->_crop($resizeWidth, $resizeHeight, $w, $h, $cropX, $cropY, $resize);
	}	

	////////// ABSTRACT METHODS //////////
	
	abstract public function resize($w, $h);
	
	abstract public function rotate($angle);
	
	abstract public function flip($direction);
	
	abstract protected function _crop($resizeWidth, $resizeHeight, $w, $h, $cropX, $cropY, $resize = true);
	
	/**
	 * Adds watermark to image using a given image
	 * 
	 * @param string $watermarkImage The path to watermark image
	 * @param string $horizontalPos The horizontal position of watermark area
	 * @param string $verticalPos The vertical position of watermark area
	 * @return bool
	 */
	abstract public function addImageWatermark($watermarkImage, $horizontalPos = 'top', $verticalPos = 'left');
	
	/**
	 * Adds watermark to image using a given text
	 * 
	 * @param string $watermarkText The text
	 * @param string $horizontalPos The horizontal position of watermark area
	 * @param string $verticalPos The vertical position of watermark area
	 * @param string $options The watermark options, including the following options:
	 * - color: The font color in HEX format
	 * - font: Path of font file
	 * - font_size: The font size (without "px" at the end)
	 * - font_measured: If TRUE, the font size will be measured based on the width of image
	 * - rotation_angle
	 * - opacity
	 * @return bool
	 */
	abstract public function addTextWatermark($watermarkText, $horizontalPos = 'top', $verticalPos = 'left', $options = array());
}
