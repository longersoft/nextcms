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
 * Represents an error
 */
class Core_Models_Error extends Core_Base_Models_Entity
{
	/**
	 * Error's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'error_id'	   => null,
		'created_user' => null,
		'created_date' => null,
		'uri'		   => null,
		'module'	   => null,
		'controller'   => null,
		'action'	   => null,
		'class'		   => null,
		'file'		   => null,
		'line'		   => null,
		'message'	   => null,
		'trace'		   => null,
	);
}
