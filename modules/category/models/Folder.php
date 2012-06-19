<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a folder
 */
class Category_Models_Folder extends Core_Base_Models_Entity
{
	/**
	 * Folder's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'folder_id'	   => null,
		'user_id'	   => null,
		'entity_class' => null,
		'name'		   => null,
		'language'	   => null,
	);
}
