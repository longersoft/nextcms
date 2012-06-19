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
 * Represents a private message folder
 */
class Message_Models_Folder extends Core_Base_Models_Entity
{
	// Special folders
	// DO NOT CHANGE THESE VALUES
	
	const FOLDER_INBOX	 = 'inbox';
	const FOLDER_SENT	 = 'sent';
	const FOLDER_DRAFT	 = 'draft';
	const FOLDER_STARRED = 'starred';
	const FOLDER_TRASH	 = 'trash';
	
	/**
	 * Folder's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'folder_id'	=> null,
		'user_id'	=> null,
		'name'		=> null,
	);
}
