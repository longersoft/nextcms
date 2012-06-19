<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	models
 * @since		1.0
 * @version		2012-04-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_Models_Dao_Adapters_Pdo_Mysql_Category extends Core_Base_Models_Dao
	implements Category_Models_Dao_Interface_Category
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Category_Models_Category($entity);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::add()
	 */
	public function add($category)
	{
		$rightId = $category->parent_id
					? $this->_conn
						   ->select()
						   ->from($this->_prefix . 'category', array('right_id'))
						   ->where('category_id = ?', $category->parent_id)
						   ->query()
						   ->fetch()
						   ->right_id
					: $this->_conn
						   ->select()
						   ->from($this->_prefix . 'category', array('right_id' => 'MAX(right_id)'))
						   ->where('language = ?', $category->language)
						   ->query()
						   ->fetch()
						   ->right_id + 1;

		$query = sprintf("UPDATE " . $this->_prefix . "category
						  SET left_id = IF(left_id > %s, left_id + 2, left_id),
							  right_id = IF(right_id >= %s, right_id + 2, right_id)
						  WHERE language = %s",
						  $this->_conn->quote($rightId),
						  $this->_conn->quote($rightId),
						  $this->_conn->quote($category->language));
		$this->_conn->query($query);

		$data = array(
			'parent_id'		   => $category->parent_id ? $category->parent_id : 0, 
			'left_id'		   => $rightId, 
			'right_id'		   => $rightId + 1, 
			'user_id'		   => $category->user_id,
			'module'		   => $category->module,
			'name'			   => $category->name,
			'slug'			   => $category->slug, 
			'image'			   => $category->image,
			'meta_description' => $category->meta_description,
			'meta_keyword'	   => $category->meta_keyword,
			'language'		   => $category->language,
			'translations'	   => $category->translations,
		);
		
		// When move category, back-up the original category's Id
		if ($category->category_id) {
			$data['category_id'] = $category->category_id;
		}
		$this->_conn->insert($this->_prefix . 'category', $data);
		$categoryId = $category->category_id ? $category->category_id : $this->_conn->lastInsertId($this->_prefix . 'category');
		
		// Update translations
		if (!$category->translations) {
			$this->_conn->update($this->_prefix . 'category', 
								array(
									'translations' => Zend_Json::encode(array($category->language => (string) $categoryId)),
								),
								array(
									'category_id = ?' => $categoryId,
								));
		} else {
			$translations = Zend_Json::decode($category->translations);
			$translations[$category->language] = (string) $categoryId;
			
			$this->_conn->update($this->_prefix . 'category', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $category->translations,
								));
		}
		
		return $categoryId;
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::delete()
	 */
	public function delete($category)
	{
		$this->_conn->delete($this->_prefix . 'category',
							array(
								'category_id = ?' => $category->category_id,
							));
		$this->_conn->update($this->_prefix . 'category',
							array(
								'parent_id'  => $category->parent_id,
							),
							array(
								'parent_id = ?' => $category->category_id,
							));
		$this->_conn->update($this->_prefix . 'category',
							array(
								'left_id'  => new Zend_Db_Expr('left_id - 1'),
								'right_id' => new Zend_Db_Expr('right_id - 1'),
							),
							array(
								'language = ?' => $category->language,
								'left_id >= ?' => $category->left_id,
								'left_id <= ?' => $category->right_id,
							));
		$this->_conn->update($this->_prefix . 'category',
							array(
								'right_id' => new Zend_Db_Expr('right_id - 2'),
							),
							array(
								'language = ?' => $category->language,
								'right_id > ?' => $category->right_id,
							));
		$this->_conn->update($this->_prefix . 'category',
							array(
								'left_id' => new Zend_Db_Expr('left_id - 2'),
							),
							array(
								'language = ?' => $category->language,
								'left_id > ?' => $category->right_id,
							));
		
		if ($category->translations) {
			$translations = Zend_Json::decode($category->translations);
			unset($translations[$category->language]);
			
			$this->_conn->update($this->_prefix . 'category', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $category->translations,
								));
		}
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::getById()
	 */
	public function getById($categoryId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'category')
					->where('category_id = ?', $categoryId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Category_Models_Category($row);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::getBySlug()
	 */
	public function getBySlug($slug, $module, $language)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'category')
					->where('slug = ?', $slug)
					->where('module = ?', $module)
					->where('language = ?', $language)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Category_Models_Category($row);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::getParents()
	 */
	public function getParents($category)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('node' => $this->_prefix . 'category'), array())
					   ->from(array('parent' => $this->_prefix . 'category'))
					   ->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					   ->where('node.category_id = ?', $category->category_id)
					   ->where('node.module = ?', $category->module)
					   ->where('node.language = ?', $category->language)
					   ->where('parent.module = ?', $category->module)
					   ->where('parent.language = ?', $category->language)
					   ->order('parent.left_id')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::getTree()
	 */
	public function getTree($module, $language, $root = null)
	{
		if ($root == null) {
			$result = $this->_conn
						   ->select()
						   ->from(array('node' => $this->_prefix . 'category'))
						   ->from(array('parent' => $this->_prefix . 'category'), array('depth' => '(COUNT(parent.category_id) - 1)'))
						   ->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
						   ->where('node.module = ?', $module)
						   ->where('parent.module = ?', $module)
						   ->where('node.language = ?', $language)
						   ->where('parent.language = ?', $language)
						   ->group('node.category_id')
						   ->order('node.left_id')
						   ->query()
						   ->fetchAll();
		} else {
			// Find all nodes of sub-tree with depth
			$query = 'SELECT node.*, (COUNT(parent.category_id) - (sub_tree.depth + 1)) AS depth
						FROM ' . $this->_prefix . 'category AS node,
							' . $this->_prefix . 'category AS parent,
							' . $this->_prefix . 'category AS sub_parent,
							(
								SELECT node.category_id, (COUNT(parent.category_id) - 1) AS depth
								FROM ' . $this->_prefix . 'category AS node,
									 ' . $this->_prefix . 'category AS parent
								WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
									AND node.category_id = ' . $this->_conn->quote($root->category_id) . '
									AND node.module = ' . $this->_conn->quote($module) . '
									AND parent.module = ' . $this->_conn->quote($module) . '
									AND node.language = ' . $this->_conn->quote($language) . '
									AND parent.language = ' . $this->_conn->quote($language) . '
								GROUP BY node.category_id
								ORDER BY node.left_id
							) AS sub_tree
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.left_id BETWEEN sub_parent.left_id AND sub_parent.right_id
							AND sub_parent.category_id = sub_tree.category_id
							AND node.module = ' . $this->_conn->quote($module) . '
							AND parent.module = ' . $this->_conn->quote($module) . '
							AND node.language = ' . $this->_conn->quote($language) . '
							AND parent.language = ' . $this->_conn->quote($language) . '
						GROUP BY node.category_id
						ORDER BY node.left_id;';
			$result = $this->_conn
						   ->query($query)
						   ->fetchAll();
		}
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::move()
	 */
	public function move($category)
	{
		if ($category->new_translations && $category->new_translations == $category->translations) {
			$category->translations = null;
		}
		$this->delete($category);
		
		if ($category->new_translations) {
			$category->translations = $category->new_translations;
		}
		$this->add($category);
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::rename()
	 */
	public function rename($category)
	{
		$this->_conn->update($this->_prefix . 'category',
							array(
								'name' => $category->name,
								'slug' => $category->slug,
							), 
							array(
								'category_id = ?' => $category->category_id,
							));
	}
	
	/**
	 * @see Category_Models_Dao_Interface_Category::update()
	 */
	public function update($category)
	{
		$this->_conn->update($this->_prefix . 'category', 
							array(
								'name'			   => $category->name,
								'slug'			   => $category->slug, 
								'image'			   => $category->image,
								'meta_description' => $category->meta_description,
								'meta_keyword'	   => $category->meta_keyword,
							),
							array(
								'category_id = ?'  => $category->category_id,
							));
		
		// Update translations
		if ($category->new_translations && $category->new_translations != $category->translations) {
			$translations = Zend_Json::decode($category->translations);
			unset($translations[$category->language]);
			$this->_conn->update($this->_prefix . 'category', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $category->translations,
								));
			
			$translations = Zend_Json::decode($category->new_translations);
			$translations[$category->language] = (string) $category->category_id;
			
			$where[] = 'category_id = ' . $this->_conn->quote($category->category_id) . ' OR translations = ' . $this->_conn->quote($category->new_translations);
			$this->_conn->update($this->_prefix . 'category',
								array(
									'translations' => Zend_Json::encode($translations),
								),
								$where);
		}
	}
}
