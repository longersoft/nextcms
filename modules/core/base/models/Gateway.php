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

abstract class Core_Base_Models_Gateway
{
	/**
	 * @var Core_Services_Db_Abstract
	 */
	protected $_conn;
	
	/**
	 * Database table prefix
	 * 
	 * @var string
	 */
	protected $_prefix;
	
	/**
	 * @return void
	 */
	public function __construct($conn = null)
	{
		$this->_prefix = Core_Services_Db::getDbPrefix();
		if ($conn != null) {
			$this->setDbConnection($conn);
		}
	}
	
	/**
	 * @param Core_Services_Db_Abstract $conn
	 */
	public function setDbConnection($conn) 
	{
		$this->_conn = $conn;
	}

	/**
	 * @return Core_Services_Db_Abstract
	 */
	public function getDbConnection()
	{
		return $this->_conn;
	}
	
	/**
	 * Converts an object or array to entity instance
	 * 
	 * @param mixed $entity
	 * @return Core_Base_Models_Entity
	 */
	abstract function convert($entity);
}
