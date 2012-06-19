<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a zone
 */
class Ad_Models_Zone extends Core_Base_Models_Entity
{
	/**
	 * Zone's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'zone_id' => null,
		'name'	  => null,
		'width'   => 0,
		'height'  => 0,
	);
}
