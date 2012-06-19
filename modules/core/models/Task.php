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
 * Represents a cron task
 */
class Core_Models_Task extends Core_Base_Models_Entity
{
	/**
	 * Task's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'task_id'	  => null,
		'module'	  => null,
		'name'		  => null,
		'title'		  => null,
		'description' => null,
		'thumbnail'	  => null,
		'website'	  => null,
		'author'	  => null,
		'email'		  => null,
		'version'	  => null,
		'app_version' => null,
		'license'	  => null,
		'last_run'	  => null,
		'next_run'	  => null,
		'time_mask'	  => null,
		'options'	  => null,
	);
}
