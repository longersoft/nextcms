<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Menu_Models_Dao_Adapters_Pdo_Mysql_Menu extends Core_Base_Models_Dao
	implements Menu_Models_Dao_Interface_Menu
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Menu_Models_Menu($entity);
	}
	
	/**
	 * @see Menu_Models_Dao_Interface_Menu::add()
	 */
	public function add($menu)
	{
		$this->_conn->insert($this->_prefix . 'menu',
							array(
								'title'		   => $menu->title,
								'description'  => $menu->description,
								'created_user' => $menu->created_user,
								'created_date' => $menu->created_date,
								'language'	   => $menu->language,
								'translations' => $menu->translations,
							));
		$menuId = $this->_conn->lastInsertId($this->_prefix . 'menu');
		
		// Add menu items
		if ($menu->items && is_array($menu->items)) {
			foreach ($menu->items as $item) {
				$item['menu_id']   = $menuId;
				$item['parent_id'] = null;
				
				$this->_addItem($item);
			}
		}
		
		// Update translation
		if (!$menu->translations) {
			$this->_conn->update($this->_prefix . 'menu', 
								array(
									'translations' => Zend_Json::encode(array($menu->language => (string) $menuId)),
								),
								array(
									'menu_id = ?' => $menuId,
								));
		} else {
			$translations = Zend_Json::decode($menu->translations);
			$translations[$menu->language] = (string) $menuId;
			
			$this->_conn->update($this->_prefix . 'menu', 
								array(
									'translations'	=> Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $menu->translations,
								));
		}
		
		return $menuId;
	}
	
	/**
	 * Adds new menu item
	 * @param array $item
	 * @return string Id of newly created menu item
	 */
	private function _addItem($item)
	{
		// Add menu item
		$rightId = $item['parent_id']
					? $this->_conn
						   ->select()
						   ->from($this->_prefix . 'menu_item', array('right_id'))
						   ->where('item_id = ?', $item['parent_id'])
						   ->limit(1)
						   ->query()
						   ->fetch()
						   ->right_id
					: $this->_conn
						   ->select()
						   ->from($this->_prefix . 'menu_item', array('right_id' => 'MAX(right_id)'))
						   ->where('menu_id = ?', $item['menu_id'])
						   ->limit(1)
						   ->query()
						   ->fetch()
						   ->right_id + 1;
		
		$query = sprintf("UPDATE " . $this->_prefix . "menu_item
						  SET left_id = IF(left_id > %s, left_id + 2, left_id),
							  right_id = IF(right_id >= %s, right_id + 2, right_id)
						  WHERE menu_id = %s",
						  $this->_conn->quote($rightId),
						  $this->_conn->quote($rightId),
						  $this->_conn->quote($item['menu_id']));
		$this->_conn->query($query);
		
		$this->_conn->insert($this->_prefix . 'menu_item',
							array(
								'menu_id'	  => $item['menu_id'],
								'title'		  => $item['title'],
								'sub_title'	  => $item['sub_title'],
								'description' => $item['description'],
								'link'		  => $item['link'],
								'target'	  => $item['target'],
								'image'		  => $item['image'],
								'parent_id'	  => $item['parent_id'] ? $item['parent_id'] : 0,
								'left_id'	  => $rightId,
								'right_id'	  => $rightId + 1,
								'html_id'	  => $item['html_id'],
								'css_class'	  => $item['css_class'],
								'css_style'   => $item['css_style'],
							));
		$itemId =$this->_conn->lastInsertId($this->_prefix . 'menu_item');

		// Add its children
		if (isset($item['children']) && is_array($item['children'])) {
			foreach ($item['children'] as $child) {
				$child['menu_id']	= $item['menu_id'];
				$child['parent_id'] = $itemId;
				$this->_addItem($child);
			}
		}
		
		return $itemId;
	}
	
	/**
	 * @see Menu_Models_Dao_Interface_Menu::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'menu', array('num_menus' => 'COUNT(*)'));
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_menus;
	}
	
	/**
	 * @see Menu_Models_Dao_Interface_Menu::delete()
	 */
	public function delete($menu)
	{
		$this->_conn->delete($this->_prefix . 'menu_item',
							array(
								'menu_id = ?' => $menu->menu_id,
							));
		$this->_conn->delete($this->_prefix . 'menu',
							array(
								'menu_id = ?' => $menu->menu_id,
							));
		if ($menu->translations) {
			$translations = Zend_Json::decode($menu->translations);
			unset($translations[$menu->language]);
			
			$this->_conn->update($this->_prefix . 'menu', 
								array(
									'translations'	=> Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $menu->translations,
								));
		}
	}
	
	/**
	 * @see Menu_Models_Dao_Interface_Menu::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'menu');
		if (isset($criteria['language']) && !empty($criteria['language'])) {
			$select->where('language = ?', $criteria['language']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$keyword = addslashes($criteria['keyword']);
			$select->where("(title LIKE '%" . $keyword . "%')");
		}
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'menu_id';
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
	 * @see Menu_Models_Dao_Interface_Menu::getById()
	 */
	public function getById($menuId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'menu')
					->where('menu_id = ?', $menuId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Menu_Models_Menu($row);
	}
	
	/**
	 * @see Menu_Models_Dao_Interface_Menu::update()
	 */
	public function update($menu)
	{
		// Update menu information
		$this->_conn->update($this->_prefix . 'menu', 
							array(
								'title'		  => $menu->title,
								'description' => $menu->description,
							),
							array(
								'menu_id = ?' => $menu->menu_id,
							));
		// Remove all children
		$this->_conn->delete($this->_prefix . 'menu_item',
							array(
								'menu_id = ?' => $menu->menu_id,
							));
		// Then, add children again
		if ($menu->items && is_array($menu->items)) {
			foreach ($menu->items as $item) {
				$item['menu_id']   = $menu->menu_id;
				$item['parent_id'] = null;
				
				$this->_addItem($item);
			}
		}
		
		// Update translations
		if ($menu->new_translations && $menu->new_translations != $menu->translations) {
			$translations = Zend_Json::decode($menu->translations);
			unset($translations[$menu->language]);
			$this->_conn->update($this->_prefix . 'menu', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								array(
									'translations = ?' => $menu->translations,
								));
			
			$translations = Zend_Json::decode($menu->new_translations);
			$translations[$menu->language] = (string) $menu->menu_id;
			$where[] = 'menu_id = ' . $this->_conn->quote($menu->menu_id) . ' OR translations = ' . $this->_conn->quote($menu->new_translations);
			$this->_conn->update($this->_prefix . 'menu', 
								array(
									'translations' => Zend_Json::encode($translations),
								),
								$where);
		}
	}
}
