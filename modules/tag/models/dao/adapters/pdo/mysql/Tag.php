<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	models
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_Models_Dao_Adapters_Pdo_Mysql_Tag extends Core_Base_Models_Dao
	implements Tag_Models_Dao_Interface_Tag
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Tag_Models_Tag($entity);
	}
	
	/**
	 * @see Tag_Models_Dao_Interface_Tag::add()
	 */
	public function add($tag)
	{
		// Add new poll
		$this->_conn->insert($this->_prefix . 'tag',
							array(
								'tag_id'   => $tag->tag_id,
								'language' => $tag->language,
								'title'	   => $tag->title,
								'slug'	   => $tag->slug,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'tag');
	}
	
	/**
	 * @see Tag_Models_Dao_Interface_Tag::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'tag', array('num_tags' => 'COUNT(*)'));
		foreach (array('language', 'slug') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_tags;
	}
	
	/**
	 * @see Tag_Models_Dao_Interface_Tag::delete()
	 */
	public function delete($tag)
	{
		$this->_conn->delete($this->_prefix . 'tag',
							array(
								'tag_id = ?' => $tag->tag_id,
							));
		$this->_conn->delete($this->_prefix . 'tag_entity_assoc',
							array(
								'tag_id = ?' => $tag->tag_id,
							));
	}
	
	/**
	 * @see Tag_Models_Dao_Interface_Tag::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'tag');
		foreach (array('language', 'slug') as $key) {
			if (isset($criteria[$key]) && !empty($criteria[$key])) {
				$select->where($key . ' = ?', $criteria[$key]);
			}
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'slug';
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
	 * @see Tag_Models_Dao_Interface_Tag::getById()
	 */
	public function getById($tagId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'tag')
					->where('tag_id = ?', $tagId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Tag_Models_Tag($row);
	}
	
	/**
	 * @see Tag_Models_Dao_Interface_Tag::getTagCloud()
	 */
	public function getTagCloud($entityClass, $language = null, $count = 20)
	{
		$select = $this->_conn
						->select()
						->from(array('te' => $this->_prefix . 'tag_entity_assoc'), array())
						->joinInner(array('t' => $this->_prefix . 'tag'), 'te.tag_id = t.tag_id', array('tag_id', 'title', 'language', 'slug', 'weight' => 'COUNT(*)'))
						->where('te.entity_class = ?', $entityClass)
						->group('title');
		if ($language) {
			$select->where('language = ?', $language);
		}
		if (is_numeric($count)) {
			$select->limit($count);	
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Tag_Models_Dao_Interface_Tag::update()
	 */
	public function update($tag)
	{
		$this->_conn->update($this->_prefix . 'tag', 
							array(
								'language' => $tag->language,
								'title'	   => $tag->title,
								'slug'	   => $tag->slug,
							),
							array(
								'tag_id = ?' => $tag->tag_id,
							));
	}
}
