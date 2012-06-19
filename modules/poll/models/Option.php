<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a poll option
 */
class Poll_Models_Option extends Core_Base_Models_Entity
{
	/**
	 * Option's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'option_id'	  => null,
		'poll_id'	  => null,
		'ordering'	  => 0,
		'title'		  => null,
		'description' => null,
		'num_choices' => 0,
	);
}
