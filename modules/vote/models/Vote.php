<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		vote
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents a vote
 */
class Vote_Models_Vote extends Core_Base_Models_Entity
{
	/**
	 * Vote's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'vote_id'	   => null,
		'entity_id'    => null,
		'entity_class' => null,
		'vote'		   => null,
		'user_id'	   => null,
		'ip'		   => null,
	);
}
