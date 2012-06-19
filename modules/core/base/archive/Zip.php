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
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Archive_Zip extends Core_Base_Archive_Abstract
{
	/**
	 * @var Core_Base_Archive_Abstract
	 */
	private $_adapter = null;
	
	public function __construct($options = array())
	{
		parent::__construct($options);
		
		if (extension_loaded('zip')) {
			$this->_adapter = new Core_Base_Archive_Nativezip();
		}
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::isSupported()
	 */
	public function isSupported()
	{
		return ($this->_adapter == null) ? false : $this->_adapter->isSupported();
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::canCompress()
	 */
	public function canCompress()
	{
		return ($this->_adapter == null) ? false : $this->_adapter->canCompress();
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::canDecompress()
	 */
	public function canDecompress()
	{
		return ($this->_adapter == null) ? false : $this->_adapter->canDecompress();
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::compress()
	 */
	public function compress($archive, $files, $options = array())
	{
		return ($this->_adapter == null) ? false : $this->_adapter->compress($archive, $files, $options);
	}
	
	/**
	 * @see Core_Base_Archive_Abstract::decompress()
	 */
	public function decompress($archive, $destination, $options = array())
	{
		return ($this->_adapter == null) ? false : $this->_adapter->decompress($archive, $destination, $options);
	}
}
