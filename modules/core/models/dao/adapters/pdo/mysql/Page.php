<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	models
 * @since		1.0
 * @version		2012-05-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_Page extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_Page
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Core_Models_Page($entity);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::add()
	 */
	public function add($page)
	{
		$this->_conn->insert($this->_prefix . 'core_page',
							array(
								'name'			 => $page->name,
								'title'			 => $page->title,
								'route'			 => $page->route,
								'url'			 => $page->url,
								'ordering'		 => $page->ordering,
								'template'		 => $page->template,
								'cache_lifetime' => 0,
								'language'		 => $page->language,
								'translations'	 => $page->translations,
							));
		$pageId = $this->_conn->lastInsertId($this->_prefix . 'core_page');
		
		// Update translations
		if (!$page->translations) {
			$this->_conn->update($this->_prefix . 'core_page', 
								array(
									'translations' => Zend_Json::encode(array($page->language => (string) $pageId)),
								),
								array(
									'page_id = ?' => $pageId,
								));
		} else {
			$translations = Zend_Json::decode($page->translations);
			$translations[$page->language] = (string) $pageId;
			
			$this->_conn->update($this->_prefix . 'core_page', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $page->translations,
								));
		}
		
		return $pageId;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_page', array('num_pages' => 'COUNT(*)'));
		if (isset($criteria['template']) && !empty($criteria['template'])) {
			$select->where('template = ?', $criteria['template']);
		}
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		return $select->limit(1)->query()->fetch()->num_pages;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::delete()
	 */
	public function delete($page)
	{
		$this->_conn->delete($this->_prefix . 'core_page',
							array(
								'page_id = ?' => $page->page_id,
							));
		if ($page->translations) {
			$translations = Zend_Json::decode($page->translations);
			unset($translations[$page->language]);
			
			$this->_conn->update($this->_prefix . 'core_page', 
								array(
									'translations'	=> Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $page->translations,
								));
		}
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::deleteByTemplate()
	 */
	public function deleteByTemplate($template)
	{
		$this->_conn->delete($this->_prefix . 'core_page',
							array(
								'template = ?' => $template,
							));
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'core_page');
		if (isset($criteria['template']) && !empty($criteria['template'])) {
			$select->where('template = ?', $criteria['template']);
		}
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'ordering';
		}
		if (!isset($criteria['sort_dir'])) {
			$criteria['sort_dir'] = 'ASC';
		}
		$select->order($criteria['sort_by'] . " " . $criteria['sort_dir']);
		if (is_numeric($offset) && is_numeric($count)) {
			$select->limit($count, $offset);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::getById()
	 */
	public function getById($pageId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_page')
					->where('page_id = ?', $pageId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Page($row);
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::update()
	 */
	public function update($page)
	{
		$this->_conn->update($this->_prefix . 'core_page', 
							array(
								'name'			 => $page->name,
								'title'			 => $page->title,
								'route'			 => $page->route,
								'url'			 => $page->url,
								'ordering'		 => $page->ordering,
								'layout'		 => $page->layout,
								'cache_lifetime' => $page->cache_lifetime,
							),
							array(
								'page_id = ?' => $page->page_id,
							));
		
		// Update translations
		if ($page->new_translations && $page->new_translations != $page->translations) {
			$translations = Zend_Json::decode($page->translations);
			unset($translations[$page->language]);
			$this->_conn->update($this->_prefix . 'core_page', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $page->translations,
								));
			
			$translations = Zend_Json::decode($page->new_translations);
			$translations[$page->language] = (string) $page->page_id;
			$where[] = 'page_id = ' . $this->_conn->quote($page->page_id) . ' OR translations = ' . $this->_conn->quote($page->new_translations);
			$this->_conn->update($this->_prefix . 'core_page', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								$where);
		}
	}
	
	/**
	 * @see Core_Models_Dao_Interface_Page::updateLayout()
	 */
	public function updateLayout($page)
	{
		$this->_conn->update($this->_prefix . 'core_page', 
							array(
								'layout' => $page->layout,
							),
							array(
								'page_id = ?' => $page->page_id,
							));
	}
}
