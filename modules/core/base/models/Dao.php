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

abstract class Core_Base_Models_Dao
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
	protected $_prefix = '';
	
	/**
	 * The language
	 * 
	 * @var string
	 */
	protected $_lang;
	
	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->_prefix = Core_Services_Db::getDbPrefix();
	}
	
	/**
	 * @param Core_Services_Db_Abstract $conn
	 * @return Core_Base_Models_Dao
	 */
	public function setDbConnection($conn)
	{
		$this->_conn = $conn;
		return $this;
	}

	/**
	 * @return Core_Services_Db_Abstract
	 */
	public function getDbConnection()
	{
		return $this->_conn;
	}
	
	/**
	 * @param string $lang
	 * @return Core_Base_Models_Dao
	 */
	public function setlang($lang)
	{
		$this->_lang = $lang;
		return $this;
	}
	
	/**
	 * Converts an object or array to entity instance
	 * 
	 * @param mixed $entity
	 * @return Core_Base_Models_Entity
	 */
	abstract function convert($entity);
}
