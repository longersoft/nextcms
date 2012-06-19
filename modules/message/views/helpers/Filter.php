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
 * @subpackage	views
 * @since		1.0
 * @version		2012-02-28
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Message_View_Helper_Filter
{
	/**
	 * Gets this view helper instance
	 * 
	 * @return Message_View_Helper_Filter
	 */
	public function filter()
	{
		return $this;
	}
	
	/**
	 * Maps the object of filter with the translation key
	 * 
	 * @param Message_Models_Filter $filter The filter instance
	 * @return string
	 */
	public function mapObject($filter)
	{
		switch ($filter->object) {
			case Message_Models_Filter::OBJECT_CONTENT:
				return 'filter._share.content';
			case Message_Models_Filter::OBJECT_FROM:
				return 'filter._share.from';
			case Message_Models_Filter::OBJECT_SUBJECT:
			default:
				return 'filter._share.subject';
		}
	}
	
	/**
	 * Maps the condition of filter with the translation key
	 * 
	 * @param Message_Models_Filter $filter The filter instance
	 * @return string
	 */
	public function mapCondition($filter)
	{
		switch ($filter->condition) {
			case Message_Models_Filter::CONDITION_NOT_LIKE:
				return 'filter._share.isNotLike';
			case Message_Models_Filter::CONDITION_IS:
				return 'filter._share.is';
			case Message_Models_Filter::CONDITION_NOT:
				return 'filter._share.isNot';
			case Message_Models_Filter::CONDITION_BEGIN:
				return 'filter._share.beginsWith';
			case Message_Models_Filter::CONDITION_END:
				return 'filter._share.endsWith';
			case Message_Models_Filter::CONDITION_LIKE:
			default:
				return 'filter._share.isLike';
		}
	}
	
	/**
	 * Maps the action of filter with the translation key
	 * 
	 * @param string $action The action name. Can be one of values in array Message_Models_Filter::$ACTIONS
	 * @return string
	 */
	public function mapAction($action)
	{
		switch ($action) {
			case Message_Models_Filter::ACTION_MOVE:
				return 'filter._share.moveToFolder';
			case Message_Models_Filter::ACTION_MARK_READ:
				return 'filter._share.markAsRead';
			case Message_Models_Filter::ACTION_START:
				return 'filter._share.starMessage';
			case Message_Models_Filter::ACTION_DELETE:
				return 'filter._share.deleteMessage';
		}
	}
}
