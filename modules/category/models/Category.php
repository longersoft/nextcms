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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a category
 */
class Category_Models_Category extends Core_Base_Models_Entity
{
	/**
	 * Category's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'category_id'	   => null,
		'parent_id'		   => 0,
		'left_id'		   => null,
		'right_id'		   => null,
		'user_id'		   => null,
		'module'		   => null,
		'name'			   => null,
		'slug'			   => null,
		'image'			   => null,
		'meta_description' => null,
		'meta_keyword'	   => null,
		'language'		   => null,
		'translations'	   => null,
	);
}
