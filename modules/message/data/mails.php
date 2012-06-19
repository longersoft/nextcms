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
 * @subpackage	data
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Built-in templates for email. You can change it in the back-end
 * 
 * @return array
 */
return array(
	// Used when an user send new private messages to other
	// Available macros:
	//	- ###username###: The username of recipient
	//	- ###sender###: The username of sender
	//	- ###subject###: The subject of message
	//	- ###content###: The content of message
	//	- ###link###: The sign in link
	'message_sent_template' => array(
		'from_name'   => '',
		'from_email'  => '',
		'subject'     => 'New private message from ###sender###',
		'content'     => 'Hi ###username###, <br />
<br />
You have new private message sent from ###sender###:<br />
###content###<br />
You can <a href="###link###" target="_blank">sign in</a> to reply as well as manage your messages.<br />
<br />
Best regards,<br />
Administrator',
	),
);
