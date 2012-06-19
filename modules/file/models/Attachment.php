<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	models
 * @since		1.0
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents an attachment
 */
class File_Models_Attachment extends Core_Base_Models_Entity
{
	/**
	 * Attachment's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'attachment_id' => null,
		'hash'			=> null,
		'title'			=> null,
		'slug'			=> null,
		'description'	=> null,
		'name'			=> null,
		'extension'		=> null,
		'path'			=> null,
		'size'			=> 0,
		'uploaded_user' => null,
		'uploaded_date' => null,
		'num_downloads' => 0,
		'last_download' => null,
		'auth_required' => 0,
		'password'		=> null,
		'language'		=> null,
		'translations'	=> null,
	);
	
	/**
	 * @see Core_Base_Models_Entity::getId()
	 */
	public function getId()
	{
		return $this->_properties['attachment_id'];
	}
	
	/**
	 * @see Core_Base_Models_Entity::getTitle()
	 */
	public function getTitle()
	{
		return $this->_properties['title'];
	}
}
