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

class Content_Tasks_Feedimporter_Models_Dao_Adapters_Pdo_Mysql_Entry extends Core_Base_Models_Dao
	implements Content_Tasks_Feedimporter_Models_Dao_Interface_Entry
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Content_Tasks_Feedimporter_Models_Entry($entity);
	}
	
	/**
	 * @see Content_Tasks_Feedimporter_Models_Dao_Interface_Entry::exist()
	 */
	public function exist($link)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'content_feed_entry', array('entry_id'))
					->where('link = ?', $link)
					->limit(1)
					->query()
					->fetch();
		return ($row == null) ? false : $row->entry_id;
	}
	
	/**
	 * @see Content_Tasks_Feedimporter_Models_Dao_Interface_Entry::add()
	 */
	public function add($entry)
	{
		$this->_conn->insert($this->_prefix . 'content_feed_entry',
							array(
								'feed_url'	   => $entry->feed_url,
								'link'		   => $entry->link,
								'article_id'   => $entry->article_id,
								'created_date' => $entry->created_date,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'content_feed_entry');
	}
}
