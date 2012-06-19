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

abstract class File_Services_Connector_Abstract
{
	protected $_connection = null;
	
	protected $_params = array();
	
	/**
	 * Creates new connection instance
	 * 
	 * @param array $params An array of connection parameters including the following keys:
	 * - server
	 * - port
	 * - user_name
	 * - password
	 */
	public function __construct($params = array())
	{
		$this->_params = $params;
	}
	
	/**
	 * @return mixed|bool
	 */
	abstract public function connect();
	
	/**
	 * @return bool
	 */
	abstract public function disconnect();
	
	/**
	 * @return mixed
	 */
	abstract public function getConnection();
}
