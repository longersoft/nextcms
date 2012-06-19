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
 * Represents a poll
 */
class Poll_Models_Poll extends Core_Base_Models_Entity
{
	/**
	 * Poll's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'poll_id'		   => null,
		'title'			   => null,
		'description'	   => null,
		'created_user'	   => null,
		'created_date'	   => null,
		'multiple_options' => 0,
		'language'		   => null,
		'translations'	   => null,
		'options'		   => null,
	);
}
