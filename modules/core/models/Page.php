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
 * @version		2012-05-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a page
 */
class Core_Models_Page extends Core_Base_Models_Entity
{
	/**
	 * Page's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'page_id'		 => null,
		'name'			 => null,
		'title'			 => null,
		'route'			 => null,
		'url'			 => '',
		'ordering'		 => 0,
		'template'		 => 'default',
		'layout'		 => null,
		'cache_lifetime' => 0,
		'language'	   	 => null,
		'translations'	 => null,
	);
}
