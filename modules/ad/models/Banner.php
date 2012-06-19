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
 * @version		2011-10-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a banner
 */
class Ad_Models_Banner extends Core_Base_Models_Entity
{
	// DO NOT CHANGE THESE CONSTANTS
	
	// Banner formats
	const FORMAT_IMAGE		= 'image';
	const FORMAT_FLASH		= 'flash';	// Accept flash, video
	const FORMAT_HTML		= 'html';	// Accept HTML, frame
	const FORMAT_JAVASCRIPT	= 'javascript';
	
	// Banner status
	const STATUS_ACTIVATED	   = 'activated';
	const STATUS_NOT_ACTIVATED = 'not_activated';
	
	/**
	 * Banner's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'banner_id'	   => null,
		'title'		   => null,
		'format'	   => self::FORMAT_IMAGE,
		'code'		   => null,
		'target'	   => '_self',
		'target_url'   => null,
		'url'		   => null,
		'status'	   => self::STATUS_NOT_ACTIVATED,
		'created_date' => null,
		'from_date'	   => null,
		'to_date'	   => null,
	);
}
