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
 * Represents a rule
 */
class Core_Models_Rule extends Core_Base_Models_Entity 
{
	// These constants are used to set value for obj_type field.
	// DO NOT CHANGE THESE VALUES
	const TYPE_USER = 'user';
	const TYPE_ROLE = 'role';
	
	/**
	 * Rule's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'rule_id' 		  => null,
		'obj_id' 		  => null,
		'obj_type' 		  => null,
		'allow' 		  => null,
		'action_name' 	  => null,
		'resource_name'   => null,
		'module_name'     => null,
		'controller_name' => null,
		'extension_type'  => null,		// Can be NULL or "module", "hook", "plugin", "task", "widget"
	);
}
