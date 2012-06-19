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

class Content_Tasks_Publisher_Models_Dao_Adapters_Pdo_Mysql_Article extends Core_Base_Models_Dao
	implements Content_Tasks_Publisher_Models_Dao_Interface_Article
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Content_Models_Article($entity);
	}
	
	/**
	 * @see Content_Tasks_Publisher_Models_Dao_Interface_Article::activate()
	 */
	public function activate($date)
	{
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'status' 		 => Content_Models_Article::STATUS_ACTIVATED,
								'activated_user' => 0,
								'activated_date' => date('Y-m-d H:i:s'),
							),
							array(
								'status <> ?'		  => Content_Models_Article::STATUS_ACTIVATED,
								'publishing_date < ?' => $date,
							));
	}
}
