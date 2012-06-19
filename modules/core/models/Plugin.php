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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a plugin
 */
class Core_Models_Plugin extends Core_Base_Models_Entity
{
	/**
	 * Plugin's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'plugin_id'	  => null,
		'ordering'	  => 0,
		'module'	  => null,
		'name'		  => null,
		'enabled'	  => 1,
		'title'		  => null,
		'description' => null,
		'thumbnail'	  => null,
		'website'	  => null,
		'author'	  => null,
		'email'		  => null,
		'version'	  => null,
		'app_version' => null,
		'license'	  => null,
		'options'	  => null,
	);
}
