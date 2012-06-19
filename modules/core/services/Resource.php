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
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Resource
{
	/**
	 * Adds new resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @return string The id of newly created resource
	 */
	public static function add($resource)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'Resource',
								))
								->setDbConnection($conn)
								->add($resource);
	}
	
	/**
	 * Gets resources of given module
	 * 
	 * @param string $module OPTIONAL The module's name
	 * @param bool $groupByExtension
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getResources($module = null, $groupByExtension = true)
	{
		$conn	= Core_Services_Db::getConnection();
		$result = Core_Services_Dao::factory(array(
										'module' => 'core',
										'name'   => 'Resource',
								   ))
								   ->setDbConnection($conn)
								   ->getResources($module);
		if (!$groupByExtension) {
			return $result;
		}
		
		$resources = array();
		foreach (array('module', 'hook', 'plugin', 'task', 'widget') as $type) {
			$resources[$type] = array();
		}
		foreach ($result as $row) {
			array_push($resources[$row->extension_type], $row);
		}
		return $resources;
	}
}
