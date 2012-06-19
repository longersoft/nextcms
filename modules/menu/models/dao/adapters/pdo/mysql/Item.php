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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Menu_Models_Dao_Adapters_Pdo_Mysql_Item extends Core_Base_Models_Dao
	implements Menu_Models_Dao_Interface_Item
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Menu_Models_Item($entity);
	}
	
	/**
	 * @see Menu_Models_Dao_Interface_Item::getItemsTree()
	 */
	public function getItemsTree($menu)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('node' => $this->_prefix . 'menu_item'))
					   ->from(array('parent' => $this->_prefix . 'menu_item'), array('depth' => '(COUNT(parent.item_id) - 1)'))
					   ->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					   ->where('node.menu_id = ?', $menu->menu_id)
					   ->where('parent.menu_id = ?', $menu->menu_id)
					   ->group('node.item_id')
					   ->order('node.left_id')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
}
