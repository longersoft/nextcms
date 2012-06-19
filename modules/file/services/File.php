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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Services_File
{
	/**
	 * Gets the adapter instance
	 * 
	 * @param string $adapter
	 * @param array $connectionParams
	 * @return File_Services_File_Abstract
	 */
	public static function factory($adapter = 'local', $connectionParams = array())
	{
		switch ($adapter) {
			case 'local':
				return new File_Services_File_Local();
				break;
			case 'ftp':
				return new File_Services_File_Ftp($connectionParams);
				break;
			default:
				$class = new $adapter($connectionParams);
				if (!($class instanceof File_Services_File_Abstract)) {
					throw new Exception('The ' . $adapter . ' has to extends from File_Services_File_Abstract');
				}
				return $class;
				break;
		}
	}
}
