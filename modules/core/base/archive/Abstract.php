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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

abstract class Core_Base_Archive_Abstract
{
	protected $_options = array();
	
	public function __construct($options = array())
	{
		$this->_options = $options;
	}
	
	/**
	 * Checks if the adapter is supported
	 * 
	 * @return bool
	 */
	abstract public function isSupported();
	
	/**
	 * Checks if the adapter can compress file
	 * 
	 * @return bool
	 */
	abstract public function canCompress();
	
	/**
	 * Checks if the adapter can decompress file
	 * 
	 * @return bool
	 */
	abstract public function canDecompress();
	
	/**
	 * Compress file
	 * 
	 * @param string $archive
	 * @param array $files
	 * @param array $options
	 * @return bool
	 */
	abstract public function compress($archive, $files, $options = array());
	
	/**
	 * Decompress file
	 * 
	 * @param string $archive The path of archive file
	 * @param string $destination The path of target
	 * @param array $options Decompress options
	 * @return bool
	 */
	abstract public function decompress($archive, $destination, $options = array());
}
