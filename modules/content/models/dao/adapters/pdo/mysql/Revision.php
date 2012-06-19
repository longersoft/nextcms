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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Models_Dao_Adapters_Pdo_Mysql_Revision extends Core_Base_Models_Dao
	implements Content_Models_Dao_Interface_Revision
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Content_Models_Revision($entity);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Revision::add()
	 */
	public function add($revision)
	{
		$this->_conn->update($this->_prefix . 'content_revision', 
							array(
								'is_active' => 0,
							),
							array(
								'article_id = ?' => $revision->article_id,
							));
		$this->_conn->insert($this->_prefix . 'content_revision',
							array(
								'comment'		   => $revision->comment,
								'is_active'		   => $revision->is_active,
								'versioning_date'  => $revision->versioning_date,
								'article_id'	   => $revision->article_id,
								'category_id'	   => $revision->category_id,
								'categories'	   => $revision->categories ? implode(',', $revision->categories) : null,
								'tags'			   => $revision->tags ? implode(',', $revision->tags) : null,
								'type'			   => $revision->type,
								'title'			   => $revision->title,
								'sub_title'		   => $revision->sub_title,
								'slug'			   => $revision->slug,
								'description'	   => $revision->description,
								'meta_description' => $revision->meta_description,
								'meta_keyword'	   => $revision->meta_keyword,
								'content'		   => $revision->content,
								'layout'		   => $revision->layout,
								'author'		   => $revision->author,
								'credit'		   => $revision->credit,
								'featured'		   => $revision->featured,
								'image_icon'	   => $revision->image_icon,
								'video_icon'	   => $revision->video_icon,
								'num_views'		   => $revision->num_views,
								'image_square'	   => $revision->image_square,
								'image_thumbnail'  => $revision->image_thumbnail,
								'image_small'	   => $revision->image_small,
								'image_crop'	   => $revision->image_crop,
								'image_medium'	   => $revision->image_medium,
								'image_large'	   => $revision->image_large,
								'image_original'   => $revision->image_original,
								'cover_title'	   => $revision->cover_title,
								'created_user'	   => $revision->created_user,
								'created_date'	   => $revision->created_date,
								'updated_user'	   => $revision->updated_user,
								'updated_date'	   => $revision->updated_date,
								'activated_user'   => $revision->activated_user,
								'activated_date'   => $revision->activated_date,
								'publishing_date'  => $revision->publishing_date,
								'status'		   => $revision->status,
								'language'		   => $revision->language,
								'translations'	   => $revision->translations,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'content_revision');
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Revision::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'content_revision', array('num_revisions' => 'COUNT(*)'));
		if (isset($criteria['article_id']) && !empty($criteria['article_id'])) {
			$select->where('article_id = ?', $criteria['article_id']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(comment title LIKE '%" . $keyword . "%' OR title LIKE '%" . $keyword . "%' OR content LIKE '%" . $keyword . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_revisions;
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Revision::delete()
	 */
	public function delete($revision)
	{
		$this->_conn->delete($this->_prefix . 'content_revision',
							array(
								'revision_id = ?' => $revision->revision_id,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Revision::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'content_revision');
		if (isset($criteria['article_id']) && !empty($criteria['article_id'])) {
			$select->where('article_id = ?', $criteria['article_id']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(comment title LIKE '%" . $keyword . "%' OR title LIKE '%" . $keyword . "%' OR content LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'revision_id';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'DESC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Revision::getById()
	 */
	public function getById($revisionId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'content_revision')
					->where('revision_id = ?', $revisionId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Content_Models_Revision($row);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Revision::restore()
	 */
	public function restore($revision)
	{
		$this->_conn->update($this->_prefix . 'content_revision', 
							array(
								'is_active' => 0,
							),
							array(
								'article_id = ?' => $revision->article_id,
							));
		$this->_conn->update($this->_prefix . 'content_revision', 
							array(
								'is_active' => 1,
							),
							array(
								'revision_id = ?' => $revision->revision_id,
							));
		// Update article
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'category_id'	   => $revision->category_id,
								'categories'	   => $revision->categories,
								'type'			   => $revision->type,
								'title'			   => $revision->title,
								'sub_title'		   => $revision->sub_title,
								'slug'			   => $revision->slug,
								'description'	   => $revision->description,
								'meta_description' => $revision->meta_description,
								'meta_keyword'	   => $revision->meta_keyword,
								'content'		   => $revision->content,
								'layout'		   => $revision->layout,
								'author'		   => $revision->author,
								'credit'		   => $revision->credit,
								'featured'		   => $revision->featured,
								'image_icon'	   => $revision->image_icon,
								'video_icon'	   => $revision->video_icon,
								'image_square'	   => $revision->image_square,
								'image_thumbnail'  => $revision->image_thumbnail,
								'image_small'	   => $revision->image_small,
								'image_crop'	   => $revision->image_crop,
								'image_medium'	   => $revision->image_medium,
								'image_large'	   => $revision->image_large,
								'image_original'   => $revision->image_original,
								'cover_title'	   => $revision->cover_title,
								'updated_user'	   => $revision->updated_user,
								'updated_date'	   => $revision->updated_date,
								'activated_user'   => $revision->activated_user,
								'activated_date'   => $revision->activated_date,
								'publishing_date'  => $revision->publishing_date,
							),
							array(
								'article_id = ?' => $revision->article_id,
							));
		$this->_conn->delete($this->_prefix . 'content_article_category_assoc',
							array(
								'article_id = ?' => $revision->article_id,
							));
		if ($revision->categories) {
			foreach (explode(',', $revision->categories) as $categoryId) {
				$this->_conn->insert($this->_prefix . 'content_article_category_assoc',
									array(
										'article_id'  => $revision->article_id,
										'category_id' => $categoryId,
									));
			}
		}
	}
}
