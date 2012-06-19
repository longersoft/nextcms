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
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Menu_Services_Menu
{
	/**
	 * Adds new menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return string Id of newly created menu
	 */
	public static function add($menu)
	{
		if (!$menu || !($menu instanceof Menu_Models_Menu)) {
			throw new Exception('The param is not an instance of Menu_Models_Menu');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'menu',
									'name'   => 'Menu',
								))
								->setDbConnection($conn)
								->add($menu);
	}
	
	/**
	 * Gets the number of menus by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'menu',
									'name'   => 'Menu',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Deletes given menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return bool
	 */
	public static function delete($menu)
	{
		if (!$menu || !($menu instanceof Menu_Models_Menu)) {
			throw new Exception('The param is not an instance of Menu_Models_Menu');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'menu',
							'name'   => 'Menu',
						 ))
						 ->setDbConnection($conn)
						 ->delete($menu);
		return true;
	}

	/**
	 * Finds menus by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'menu',
									'name'   => 'Menu',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets menu instance by given Id
	 * 
	 * @param string $menuId Menu's Id
	 * @return Menu_Models_Menu|null
	 */
	public static function getById($menuId)
	{
		if ($menuId == null || empty($menuId)) {
			return null;
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'menu',
									'name'   => 'Menu',
								))
								->setDbConnection($conn)
								->getById($menuId);
	}
	
	/**
	 * Gets items tree of given menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return Core_Base_Models_RecordSet
	 */
	public static function getItemsTree($menu)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'menu',
									'name'   => 'Item',
								))
								->setDbConnection($conn)
								->getItemsTree($menu);
	}
	
	/**
	 * Gets items of given menu for building Dojo tree 
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return array
	 */
	public static function getItemsTreeData($menu)
	{
		// Map item's Id with its children
		$children = array();
		$items	  = self::getItemsTree($menu);
		foreach ($items as $item) {
			$parentId = $item->parent_id;
			if ($parentId == null) {
				continue;
			}
			$parentId = (string) $parentId;
			if (!isset($children[$parentId])) {
				$children[$parentId] = array();
			}
			$children[$parentId][] = $item;
		}
		$data = array(
			'identifier' => 'item_id',
			'label'		 => 'title',
			'items'		 => self::_getItemsTreeData('0', $children),
		);
		return $data;
	}
	
	/**
	 * @param string $parentId Id of parent item
	 * @param array $children Its children
	 * @return array
	 */
	private static function _getItemsTreeData($parentId, $children)
	{
		$parentId = (string) $parentId;
		if (!isset($children[$parentId])) {
			return array();
		}
		$items = array();
		foreach ($children[$parentId] as $child) {
			$item = array(
				'item_id'		=> $child->item_id,
				'title'			=> $child->title,
				'sub_title'		=> $child->sub_title,
				'description'	=> $child->description,
				'link'			=> $child->link,
				'target'		=> $child->target,
				'image'			=> $child->image,
				'html_id'	    => $child->html_id,
				'css_class'	    => $child->css_class,
				'css_style'		=> $child->css_style,
				'numberOfItems' => isset($children[$child->item_id . '']) ? count($children[$child->item_id . '']) : 0,
			);
			if ($item['numberOfItems'] > 0) {
				$item['children'] = self::_getItemsTreeData($child->item_id, $children);
			}
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Updates given menu
	 * 
	 * @param Menu_Models_Menu $menu The menu instance
	 * @return bool
	 */
	public static function update($menu)
	{
		if (!$menu || !($menu instanceof Menu_Models_Menu)) {
			throw new Exception('The param is not an instance of Menu_Models_Menu');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'menu',
							'name'   => 'Menu',
						 ))
						 ->setDbConnection($conn)
						 ->update($menu);
		return true;
	}
}
