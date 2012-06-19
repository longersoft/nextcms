<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	models
 * @since		1.0
 * @version		2012-01-11
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a tag
 */
class Tag_Models_Tag extends Core_Base_Models_Entity
{
	/**
	 * Tag's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'tag_id'   => null,
		'language' => null,
		'title'	   => null,
		'slug'	   => null,
	);
}
