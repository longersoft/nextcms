<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	bootstrap
 * @since		1.0
 * @version		2012-03-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Comment_Bootstrap extends Core_Base_Application_Module_Bootstrap
{
	/**
	 * Registers hooks
	 * 
	 * @return void
	 */
	protected function _initHooks()
	{
		Core_Base_Hook_Registry::getInstance()
			->register('Vote_Services_Vote_Add', 'Comment_Services_Comment::increaseNumVotes');
	}
}
