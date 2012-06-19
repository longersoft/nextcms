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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_MailQueue extends Core_Base_Models_Entity
{
	/**
	 * Queue's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'mail_id'	   => null,
		'from_name'	   => null,
		'from_email'   => null,
		'to_name'	   => null,
		'to_email'	   => null,
		'subject'	   => null,
		'content'	   => null,
		'num_attempts' => 0,
		'success'	   => 0,
		'last_attempt' => null,
		'queued_date'  => null,
		'sent_date'	   => null,
	);
}
