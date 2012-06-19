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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a menu
 */
class Menu_Models_Menu extends Core_Base_Models_Entity
{
	/**
	 * Menu's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'menu_id'	   => null,
		'title'		   => null,
		'description'  => null,
		'created_user' => null,
		'created_date' => null,
		'items'		   => null,
		'language'	   => null,
		'translations' => null,
	);
}
