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
 * @version		2012-05-04
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Models_Dao_Adapters_Pdo_Mysql_Article extends Core_Base_Models_Dao
	implements Content_Models_Dao_Interface_Article
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Content_Models_Article($entity);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::add()
	 */
	public function add($article)
	{
		$this->_conn->insert($this->_prefix . 'content_article',
							array(
								'category_id'	   => $article->category_id,
								'categories'	   => $article->categories ? implode(',', $article->categories) : null,
								'type'			   => $article->type,
								'title'			   => $article->title,
								'sub_title'		   => $article->sub_title,
								'slug'			   => $article->slug,
								'description'	   => $article->description,
								'meta_description' => $article->meta_description,
								'meta_keyword'	   => $article->meta_keyword,
								'content'		   => $article->content,
								'layout'		   => $article->layout,
								'user_name'		   => $article->user_name,
								'author'		   => $article->author,
								'credit'		   => $article->credit,
								'featured'		   => $article->featured,
								'image_icon'	   => $article->image_icon,
								'video_icon'	   => $article->video_icon,
								'num_views'		   => $article->num_views,
								'image_square'	   => $article->image_square,
								'image_thumbnail'  => $article->image_thumbnail,
								'image_small'	   => $article->image_small,
								'image_crop'	   => $article->image_crop,
								'image_medium'	   => $article->image_medium,
								'image_large'	   => $article->image_large,
								'image_original'   => $article->image_original,
								'cover_title'	   => $article->cover_title,
								'created_user'	   => $article->created_user,
								'created_date'	   => $article->created_date,
								'publishing_date'  => $article->publishing_date,
								'status'		   => $article->status,
								'language'		   => $article->language,
								'translations'	   => $article->translations,
							));
		$articleId = $this->_conn->lastInsertId($this->_prefix . 'content_article');
		
		if ($article->categories) {
			foreach ($article->categories as $categoryId) {
				$this->_conn->insert($this->_prefix . 'content_article_category_assoc',
									array(
										'article_id'  => $articleId,
										'category_id' => $categoryId,
									));
			}
		}
		
		// Update translations
		if (!$article->translations) {
			$this->_conn->update($this->_prefix . 'content_article', 
								array(
									'translations' => Zend_Json::encode(array($article->language => (string) $articleId)),
								),
								array(
									'article_id = ?' => $articleId,
								));
		} else {
			$translations = Zend_Json::decode($article->translations);
			$translations[$article->language] = (string) $articleId;
			
			$this->_conn->update($this->_prefix . 'content_article', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $article->translations,
								));
		}
		
		return $articleId;
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('a' => $this->_prefix . 'content_article'), array('num_articles' => 'COUNT(*)'));
		if (isset($criteria['category_id']) && !empty($criteria['category_id'])) {
			$select->joinLeft(array('ac' => $this->_prefix . 'content_article_category_assoc'), 'a.article_id = ac.article_id', array())
				   ->where('ac.category_id = ?', $criteria['category_id']);
		}
		if (isset($criteria['folder_id']) && !empty($criteria['folder_id'])) {
			$select->joinLeft(array('af' => $this->_prefix . 'content_article_folder_assoc'), 'a.article_id = af.article_id', array())
				   ->where('af.folder_id = ?', $criteria['folder_id']);
		}
		
		foreach (array('type', 'language', 'created_user', 'featured', 'image_icon', 'video_icon', 'slug') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('a.' . $key . ' = ?', $criteria[$key]);
			}
		}
		if (isset($criteria['tag']) && ($criteria['tag'] instanceof Tag_Models_Tag)) {
			$select->joinLeft(array('te' => $this->_prefix . 'tag_entity_assoc'), 'a.article_id = te.entity_id', array())
				   ->where('te.entity_class = ?', 'Content_Models_Article')
				   ->where('te.tag_id = ?', $criteria['tag']->tag_id);
		}
		
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('a.status = ?', $criteria['status']);
		} else {
			$select->where('a.status != ?', Content_Models_Article::STATUS_DELETED);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$select->where("(a.title LIKE '%" . addslashes($criteria['keyword']) . "%' OR a.content LIKE '%" . addslashes($criteria['keyword']) . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_articles;
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::countByDate()
	 */
	public function countByDate($criteria = array(), $lowerDate, $upperDate)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('a' => $this->_prefix . 'content_article'), array('num_articles' => 'COUNT(a.article_id)', '_date' => 'DATE(activated_date)'));
		if (isset($criteria['category_id']) && !empty($criteria['category_id'])) {
			$select->joinLeft(array('ac' => $this->_prefix . 'content_article_category_assoc'), 'a.article_id = ac.article_id', array())
				   ->where('ac.category_id = ?', $criteria['category_id']);
		}
		foreach (array('type', 'language', 'status') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('a.' . $key . ' = ?', $criteria[$key]);
			}
		}
		
		$result = $select->where('a.activated_date >= ?', $lowerDate)
						 ->where('a.activated_date <= ?', $upperDate)
						 ->group('DATE(a.activated_date)')
						 ->query()
						 ->fetchAll();
		$return = array();
		foreach ($result as $row) {
			$return[$row->_date] = $row->num_articles;
		}
		ksort($return);
		return $return;
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::delete()
	 */
	public function delete($article)
	{
		$this->_conn->delete($this->_prefix . 'content_article_category_assoc',
							array(
								'article_id = ?' => $article->article_id,
							));
		$this->_conn->delete($this->_prefix . 'content_article',
							array(
								'article_id = ?' => $article->article_id,
							));
							
		if ($article->translations) {
			$translations = Zend_Json::decode($article->translations);
			unset($translations[$article->language]);
			
			$this->_conn->update($this->_prefix . 'content_article', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $article->translations,
								));
		}
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::emptyTrash()
	 */
	public function emptyTrash()
	{
		$articleIdQuery = 'SELECT article_id FROM ' . $this->_prefix . 'content_article
						   WHERE status = "' . $this->_conn->quote(Content_Models_Article::STATUS_DELETED) . '"';
		$this->_conn->delete($this->_prefix . 'content_article_category_assoc',
							array(
								'article_id IN (?)' => new Zend_Db_Expr($articleIdQuery),
							));
		$this->_conn->delete($this->_prefix . 'content_article',
							array(
								'status = ?' => Content_Models_Article::STATUS_DELETED,
							));
		// FIXME: Update translations
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('a' => $this->_prefix . 'content_article'));
		if (isset($criteria['article_ids']) && !empty($criteria['article_ids'])) {
			$ids = is_array($criteria['article_ids']) ? implode(',', $criteria['article_ids']) : $criteria['article_ids']; 
			$select->where('article_id IN (?)', new Zend_Db_Expr($ids));
		}
		if (isset($criteria['category_id']) && !empty($criteria['category_id'])) {
			$select->joinLeft(array('ac' => $this->_prefix . 'content_article_category_assoc'), 'a.article_id = ac.article_id', array())
				   ->where('ac.category_id = ?', $criteria['category_id']);
		}
		if (isset($criteria['folder_id']) && !empty($criteria['folder_id'])) {
			$select->joinLeft(array('af' => $this->_prefix . 'content_article_folder_assoc'), 'a.article_id = af.article_id', array())
				   ->where('af.folder_id = ?', $criteria['folder_id']);
		}
		if (isset($criteria['tag']) && ($criteria['tag'] instanceof Tag_Models_Tag)) {
			$select->joinLeft(array('te' => $this->_prefix . 'tag_entity_assoc'), 'a.article_id = te.entity_id', array())
				   ->where('te.entity_class = ?', 'Content_Models_Article')
				   ->where('te.tag_id = ?', $criteria['tag']->tag_id);
		}
		
		foreach (array('type', 'language', 'created_user', 'featured', 'image_icon', 'video_icon', 'slug') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('a.' . $key . ' = ?', $criteria[$key]);
			}
		}
		
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('a.status = ?', $criteria['status']);
		} else {
			$select->where('a.status != ?', Content_Models_Article::STATUS_DELETED);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$select->where("(a.title LIKE '%" . addslashes($criteria['keyword']) . "%' OR a.content LIKE '%" . addslashes($criteria['keyword']) . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'a.article_id';
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
	 * @see Content_Models_Dao_Interface_Article::findByDate()
	 */
	public function findByDate($criteria, $lowerDate, $upperDate)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('a' => $this->_prefix . 'content_article'));
		if (isset($criteria['category_id']) && !empty($criteria['category_id'])) {
			$select->joinLeft(array('ac' => $this->_prefix . 'content_article_category_assoc'), 'a.article_id = ac.article_id', array())
				   ->where('ac.category_id = ?', $criteria['category_id']);
		}
		foreach (array('type', 'status') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where('a.' . $key . ' = ?', $criteria[$key]);
			}
		}
		$result = $select->where('a.activated_date >= ?', $lowerDate)
						 ->where('a.activated_date <= ?', $upperDate)
						 ->query()
						 ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::getById()
	 */
	public function getById($articleId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'content_article')
					->where('article_id = ?', $articleId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Content_Models_Article($row);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::increaseNumViews()
	 */
	public function increaseNumViews($article)
	{
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'num_views' => new Zend_Db_Expr('num_views + 1'),
							),
							array(
								'article_id = ?' => $article->article_id,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::update()
	 */
	public function update($article)
	{
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'category_id'	   => $article->category_id,
								'categories'	   => $article->categories ? implode(',', $article->categories) : $article->category_id,
								'type'			   => $article->type,
								'title'			   => $article->title,
								'sub_title'		   => $article->sub_title,
								'slug'			   => $article->slug,
								'description'	   => $article->description,
								'meta_description' => $article->meta_description,
								'meta_keyword'	   => $article->meta_keyword,
								'content'		   => $article->content,
								'layout'		   => $article->layout,
								'author'		   => $article->author,
								'credit'		   => $article->credit,
								'featured'		   => $article->featured,
								'image_icon'	   => $article->image_icon,
								'video_icon'	   => $article->video_icon,
								'image_square'	   => $article->image_square,
								'image_thumbnail'  => $article->image_thumbnail,
								'image_small'	   => $article->image_small,
								'image_crop'	   => $article->image_crop,
								'image_medium'	   => $article->image_medium,
								'image_large'	   => $article->image_large,
								'image_original'   => $article->image_original,
								'cover_title'	   => $article->cover_title,
								'updated_user'	   => $article->updated_user,
								'updated_date'	   => $article->updated_date,
								'publishing_date'  => $article->publishing_date,
							),
							array(
								'article_id = ?' => $article->article_id,
							));
		$this->_conn->delete($this->_prefix . 'content_article_category_assoc',
							array(
								'article_id = ?' => $article->article_id,
							));
		if ($article->categories) {
			foreach ($article->categories as $categoryId) {
				$this->_conn->insert($this->_prefix . 'content_article_category_assoc',
									array(
										'article_id'  => $article->article_id,
										'category_id' => $categoryId,
									));
			}
		}
		
		// Update translations
		if ($article->new_translations && $article->new_translations != $article->translations) {
			$translations = Zend_Json::decode($article->translations);
			unset($translations[$article->language]);
			$this->_conn->update($this->_prefix . 'content_article', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $article->translations,
								));
			
			$translations = Zend_Json::decode($article->new_translations);
			$translations[$article->language] = (string) $article->article_id;
			
			$where[] = 'article_id = ' . $this->_conn->quote($article->article_id) . ' OR translations = ' . $this->_conn->quote($article->new_translations);
			$this->_conn->update($this->_prefix . 'content_article',
								array(
									'translations' => Zend_Json::encode($translations),
								),
								$where);
		}
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::updateCover()
	 */
	public function updateCover($article)
	{
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'image_square'	  => $article->image_square,
								'image_thumbnail' => $article->image_thumbnail,
								'image_small'	  => $article->image_small,
								'image_crop'	  => $article->image_crop,
								'image_medium'	  => $article->image_medium,
								'image_large'	  => $article->image_large,
								'image_original'  => $article->image_original,
							),
							array(
								'article_id = ?' => $article->article_id,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::updateOrder()
	 */
	public function updateOrder($articleId, $categoryId, $index)
	{
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'ordering' => $index,
							),
							array(
								'article_id = ?'  => $articleId,
								'category_id = ?' => $categoryId,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::getPrevArticle()
	 */
	public function getPrevArticle($article)
	{
		$query =   'SELECT a1.* FROM ' . $this->_prefix . 'content_article AS a1
					INNER JOIN 
						(SELECT MAX(ordering) AS max_ordering
						 FROM ' . $this->_prefix . 'content_article
						 WHERE category_id = ' . $this->_conn->quote($article->category_id) . '
						 AND ordering < ' . $this->_conn->quote($article->ordering) . ') AS a2
					ON a1.ordering = a2.max_ordering
					AND a1.category_id = ' . $this->_conn->quote($article->category_id) . '
					AND a1.status = "activated"';
		$row   = $this->_conn->query($query)->fetch();
		return (null == $row) ? null : new Content_Models_Article($row);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::getNextArticle()
	 */
	public function getNextArticle($article)
	{
		$query =   'SELECT a1.* FROM ' . $this->_prefix . 'content_article AS a1
					INNER JOIN 
						(SELECT MIN(ordering) AS min_ordering
						 FROM ' . $this->_prefix . 'content_article
						 WHERE category_id = ' . $this->_conn->quote($article->category_id) . '
						 AND ordering > ' . $this->_conn->quote($article->ordering) . ') AS a2
					ON a1.ordering = a2.min_ordering
					AND a1.category_id = ' . $this->_conn->quote($article->category_id) . '
					AND a1.status = "activated"';
		$row   = $this->_conn->query($query)->fetch();
		return (null == $row) ? null : new Content_Models_Article($row);
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::updateStatus()
	 */
	public function updateStatus($article)
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$data = ($article->status == Content_Models_Article::STATUS_ACTIVATED || $article->status == Content_Models_Article::STATUS_NOT_ACTIVATED)
				? array(
					'status' 		 => $article->status,
					'activated_user' => $user->user_id,
					'activated_date' => date('Y-m-d H:i:s'),
				)
				: array(
					'status' => $article->status,
				);
		
		$this->_conn->update($this->_prefix . 'content_article',
							$data,
							array(
								'article_id = ?' => $article->article_id,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::updateUsername()
	 */
	public function updateUsername($user)
	{
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'user_name' => $user->user_name,
							),
							array(
								'created_user = ?' => $user->user_id,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::deleteTag()
	 */
	public function deleteTag($tag)
	{
		// Do nothing
		// The associations between tag and article are automatically deleted
		// after removing the tag
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::getTags()
	 */
	public function getTags($article)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('t' => $this->_prefix . 'tag'))
					   ->joinInner(array('te' => $this->_prefix . 'tag_entity_assoc'), 't.tag_id = te.tag_id', array())
					   ->where('te.entity_id = ?', $article->article_id)
					   ->where('te.entity_class = ?', get_class($article))
					   ->query()
					   ->fetchAll();
		$tags = array();
		foreach ($result as $row) {
			$tags[] = new Tag_Models_Tag($row);
		}
		return $tags;
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::setTags()
	 */
	public function setTags($article, $tags)
	{
		$class = get_class($article);
		$this->_conn->delete($this->_prefix . 'tag_entity_assoc',
							array(
								'entity_id = ?'	   => $article->article_id,
								'entity_class = ?' => $class,
							));
		if ($tags) {
			foreach ($tags as $tag) {
				$this->_conn->insert($this->_prefix . 'tag_entity_assoc',
									array(
										'tag_id'	   => $tag->tag_id,
										'entity_id'	   => $article->article_id,
										'entity_class' => $class,
									));
			}
		}
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::increaseNumComments()
	 */
	public function increaseNumComments($comment, $inc)
	{
		$numComments = 'num_comments' . $inc;
		$this->_conn->update($this->_prefix . 'content_article',
							array(
								'num_comments' => new Zend_Db_Expr($numComments),
							),
							array(
								'article_id = ?' => $comment->entity_id,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::addToFolder()
	 */
	public function addToFolder($articleId, $folderId)
	{
		$this->_conn->delete($this->_prefix . 'content_article_folder_assoc',
							array(
								'article_id = ?' => $articleId,
								'folder_id = ?'  => $folderId,
							));
		$this->_conn->insert($this->_prefix . 'content_article_folder_assoc',
							array(
								'article_id' => $articleId,
								'folder_id'	 => $folderId,
							));
	}
	
	/**
	 * @see Content_Models_Dao_Interface_Article::removeFromFolder()
	 */
	public function removeFromFolder($articleId, $folderId)
	{
		$this->_conn->delete($this->_prefix . 'content_article_folder_assoc',
							array(
								'article_id = ?' => $articleId,
								'folder_id = ?'  => $folderId,
							));
	}
}
