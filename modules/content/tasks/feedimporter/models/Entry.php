<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Tasks_Feedimporter_Models_Entry extends Core_Base_Models_Entity
{
	/**
	 * Entry's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'entry_id'	   => null,
		'feed_url'	   => null,
		'link'		   => null,
		'article_id'   => null,
		'created_date' => null,
	);
}
