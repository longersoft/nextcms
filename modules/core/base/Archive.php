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

class Core_Base_Archive
{
	/**
	 * Gets an instance of archive class
	 * 
	 * @param string $adapter
	 * @param array $options
	 * @return Core_Base_Archive_Abstract
	 */
	public static function factory($adapter, $options = array())
	{
		$class	  = __CLASS__ . '_' . ucfirst($adapter);
		$instance = null;
		if (class_exists($class)) {
			$instance = new $class($options);
		} else if (class_exists($adapter)) {
			$instance = new $adapter($options);
		}

		if ($instance && $instance instanceof Core_Base_Archive_Abstract) {
			return $instance;
		} else {
			throw new Exception('The ' . $adapter . ' has to extend from Core_Base_Archive_Abstract');
		}

		throw new Exception('Cannot find the ' . $adapter . ' class');
	}
}
