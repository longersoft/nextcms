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
	// The template which is used to activate new registered account
	'activating_account_template' => array(
		'from_name'   => '',
		'from_email'  => '',
		'reply_name'  => '',
		'reply_email' => '',
		'subject'     => 'Activation ###username### account',
		'content'     => 'Hi ###username###, <br />
<br />
You have just registered a new account with the username of ###username###.<br />
Click the following link to activate your account, and then you can sign in our website:<br />
###link###<br />
<br />
Best regards,<br />
Administrator'
	),
	
	// Used to send new password to user
	'sending_password_template' => array(
		'from_name'   => '',
		'from_email'  => '',
		'reply_name'  => '',
		'reply_email' => '',
		'subject'     => 'New password for ###username###',
		'content'     => 'Hi ###username###, <br />
<br />
Below are the account information which can be used to access <a href="###link###">our website</a>:<br />
- Username: ###username###<br />
- Password: ###password###<br />
<br />
Important notice: Please remember to CHANGE the password after logging in.<br />
<br />
Best regards,<br />
Administrator',
	),
);
