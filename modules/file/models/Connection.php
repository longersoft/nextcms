<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a connection
 */
class File_Models_Connection extends Core_Base_Models_Entity
{
	/**
	 * Connection's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'connection_id' => null,
		'name'			=> null,
		'type'			=> 'local',
		'server'		=> null,
		'port'			=> null,
		'user_name'		=> null,
		'password'		=> null,
		'init_path'		=> null,
	);
}
