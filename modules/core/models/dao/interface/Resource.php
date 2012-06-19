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
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Core_Models_Dao_Interface_Resource
{
	/**
	 * Adds new resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @return string The id of newly created resource
	 */
	public function add($resource);
	
	/**
	 * For ACL.
	 * Gets all resources associated with given module
	 * 
	 * @param string $module OPTIONAL Name of module
	 * @return Core_Base_Models_RecordSet
	 */
	public function getResources($module = null);
}
