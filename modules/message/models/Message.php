<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	models
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a private message
 */
class Message_Models_Message extends Core_Base_Models_Entity
{
	/**
	 * Message's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'message_id'   => null,
		'root_id'	   => 0,
		'sent_user'	   => null,
		'subject'	   => null,
		'content'	   => null,
		'sent_date'	   => null,
		'reply_to'	   => 0,
	);
}
