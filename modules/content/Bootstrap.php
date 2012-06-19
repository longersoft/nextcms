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
 * @subpackage	bootstrap
 * @since		1.0
 * @version		2012-05-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Bootstrap extends Core_Base_Application_Module_Bootstrap
{
	/**
	 * Registers hooks
	 * 
	 * @return void
	 */
	protected function _initHooks()
	{
		$highlight = Core_Filters_Highlight::getInstance();
		
		Core_Base_Hook_Registry::getInstance()
			->register('Tag_Services_Tag_DeleteTag', 'Content_Services_Article::deleteTag')
			->register('Comment_Services_Comment_UpdateStatus', 'Content_Services_Article::updateCommentStatus')
			->register('Core_Services_User_UpdateUsername', 'Content_Services_Article::updateUsername')
			// Parse the widgets embed in the content
			->register('Content_FilterArticleContent', 'Core_Filters_WidgetParser::parse')
			// Hightlight searching keyword
			->register('Content_FilterSearchingKeyword', array($highlight, 'setKeyword'));
	}
}
