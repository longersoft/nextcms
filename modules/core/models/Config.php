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
 * Represents a module setting
 */
class Core_Models_Config extends Core_Base_Models_Entity
{
	/**
	 * Setting's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'module' 	   => null,
		'config_key'   => null,
		'config_value' => null,
	);
}
