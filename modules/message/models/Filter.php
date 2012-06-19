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
 * Represents a message filter
 */
class Message_Models_Filter extends Core_Base_Models_Entity
{
	// DO NOT CHANGE THE FOLLOWING VALUES
	const OBJECT_SUBJECT = 'subject';
	const OBJECT_CONTENT = 'content';
	const OBJECT_FROM	 = 'from';
	
	const CONDITION_LIKE	 = 'like';
	const CONDITION_NOT_LIKE = 'not_like';
	const CONDITION_IS		 = 'is';
	const CONDITION_NOT		 = 'not';
	const CONDITION_BEGIN	 = 'begin';
	const CONDITION_END		 = 'end';
	
	const ACTION_MOVE	   = 'folder_id';
	const ACTION_MARK_READ = 'read';
	const ACTION_START	   = 'starred';
	const ACTION_DELETE    = 'deleted';
	
	/**
	 * Array of available actions
	 * 
	 * @var array
	 */
	public static $ACTIONS = array(
		self::ACTION_MOVE,
		self::ACTION_MARK_READ,
		self::ACTION_START,
		self::ACTION_DELETE
	);
	
	/**
	 * Filter's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'filter_id'	    => null,
		'user_id'	    => null,
		'object'	    => null,	// Can take one of the following values: 'subject', 'content', 'from'
		'condition'	    => null,	// Can take one of the following values: 'like', 'not_like', 'is', 'not', 'begin', 'end'
		'comparison_to' => null,
		'actions'	    => null,
		'folder_id'	    => null,
	);
	
	/**
	 * @param Message_Models_Message $message
	 * @return bool
	 */
	public function match($message)
	{
		switch ($this->object) {
			case self::OBJECT_SUBJECT:
				return $this->_match($message->subject, $this->comparison_to, $this->condition);
			case self::OBJECT_CONTENT:
				return $this->_match($message->content, $this->comparison_to, $this->condition);
			case self::OBJECT_FROM:
				return $this->_match($message->sent_user, $this->comparison_to, $this->condition);
		}
		return false;
	}
	
	/**
	 * @param string $input
	 * @param string $comparisonTo
	 * @param string $condition
	 * @return bool
	 */
	private function _match($input, $comparisonTo, $condition)
	{
		switch ($condition) {
			case self::CONDITION_LIKE:
				return (preg_match('/^(.*)' . $comparisonTo . '(.*)$/', $input) == 1);
			case self::CONDITION_NOT_LIKE:
				return (preg_match('/^(.*)' . $comparisonTo . '(.*)$/', $input) == 0);
			case self::CONDITION_IS:
				return ($input == $comparisonTo);
			case self::CONDITION_NOT:
				return ($input != $comparisonTo);
			case self::CONDITION_BEGIN:
				return (preg_match('/^' . $comparisonTo . '(.*)$/', $input) == 1);
			case self::CONDITION_END:
				return (preg_match('/^(.*)' . $comparisonTo . '$/', $input) == 1);
		}
		
		return false;
	}
}
