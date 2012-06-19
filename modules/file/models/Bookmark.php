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
 * Represents a bookmark
 */
class File_Models_Bookmark extends Core_Base_Models_Entity
{
	/**
	 * Bookmark's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'bookmark_id'	=> null,
		'connection_id' => null,
		'name'			=> null,
		'path'			=> null,
	);
}
