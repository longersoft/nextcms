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

/**
 * Represents a privilege resource
 */
class Core_Models_Resource extends Core_Base_Models_Entity
{
	/**
	 * Resource's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'resource_id' 	  => null,
		'parent_id'		  => null,		// Not used at the moment
		'description' 	  => null,
		'module_name' 	  => null,
		'controller_name' => null,
		'extension_type'  => 'module',		// Can be "module", "hook", "plugin", "task", "widget"
	);
}
