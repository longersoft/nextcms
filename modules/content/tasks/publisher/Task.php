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

class Content_Tasks_Publisher_Task extends Core_Base_Extension_Task
{
	/**
	 * @see Core_Base_Extension_Task::execute()
	 */
	public function execute($params = null)
	{
		Core_Services_Db::connect('master');
		$conn = Core_Services_Db::getConnection();
		$dao  = Core_Services_Dao::factory(array(
									'module' => 'content',
									'task'   => 'publisher',
									'name'   => 'Article',
							     ))
							     ->setDbConnection($conn);
		$dao->activate(date('Y-m-d H:i:s'));
	}
}
