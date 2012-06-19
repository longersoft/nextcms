<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	bootstrap
 * @since		1.0
 * @version		2012-03-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Bootstrap extends Core_Base_Application_Module_Bootstrap
{
	/**
	 * Registers hooks
	 * 
	 * @return void
	 */
	protected function _initHooks()
	{
		Core_Base_Hook_Registry::getInstance()
			->register('Tag_Services_Tag_DeleteTag', 'Media_Services_Photo::deleteTag')
			->register('Tag_Services_Tag_DeleteTag', 'Media_Services_Video::deleteTag')
			->register('Comment_Services_Comment_UpdateStatus', 'Media_Services_Photo::updateCommentStatus')
			->register('Comment_Services_Comment_UpdateStatus', 'Media_Services_Video::updateCommentStatus')
			->register('Vote_Services_Vote_Add', 'Media_Services_Photo::increaseNumVotes')
			->register('Vote_Services_Vote_Add', 'Media_Services_Video::increaseNumVotes')
			->register('Core_Services_User_UpdateUsername', 'Media_Services_Album::updateUsername')
			->register('Core_Services_User_UpdateUsername', 'Media_Services_Playlist::updateUsername')
			->register('Core_Services_User_UpdateUsername', 'Media_Services_Photo::updateUsername')
			->register('Core_Services_User_UpdateUsername', 'Media_Services_Video::updateUsername')
			// Hightlight searching keyword
			->register('Media_FilterSearchingKeyword', array(Core_Filters_Highlight::getInstance(), 'setKeyword'));
	}
}
