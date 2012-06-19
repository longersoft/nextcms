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

class File_Services_Connector_Local extends File_Services_Connector_Abstract
{
	/**
	 * @see File_Services_Connector_Abstract::connect()
	 */
	public function connect()
	{
		return true;
	}

	/**
	 * @see File_Services_Connector_Abstract::disconnect()
	 */
	public function disconnect()
	{
		return true;
	}

	/**
	 * @see File_Services_Connector_Abstract::getConnection()
	 */
	public function getConnection()
	{
		return true;
	}
}
