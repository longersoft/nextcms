<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a menu item
 */
class Menu_Models_Item extends Core_Base_Models_Entity
{
	/**
	 * Item's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'item_id'	  => null,
		'menu_id'	  => null,
		'title'		  => null,
		'sub_title'	  => null,
		'description' => null,
		'link'		  => null,
		'target'	  => null,
		'image'		  => null,
		'left_id'	  => null,
		'right_id'	  => null,
		'parent_id'	  => null,
		'html_id'	  => null,
		'css_class'	  => null,
		'css_style'	  => null,
	);
}
