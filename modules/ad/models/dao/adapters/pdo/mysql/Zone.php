<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	models
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_Models_Dao_Adapters_Pdo_Mysql_Zone extends Core_Base_Models_Dao
	implements Ad_Models_Dao_Interface_Zone
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Ad_Models_Zone($entity);
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Zone::add()
	 */
	public function add($zone)
	{
		$this->_conn->insert($this->_prefix . 'ad_zone',
							array(
								'name'   => $zone->name,
								'width'  => $zone->width,
								'height' => $zone->height,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'ad_zone');
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Zone::delete()
	 */
	public function delete($zone)
	{
		$this->_conn->delete($this->_prefix . 'ad_zone',
							array(
								'zone_id = ?' => $zone->zone_id,
							));
		$this->_conn->delete($this->_prefix . 'ad_banner_page_assoc',
							array(
								'zone_id = ?' => $zone->zone_id,
							));
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Zone::find()
	 */
	public function find()
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'ad_zone')
					   ->order('name ASC')
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Zone::getById()
	 */
	public function getById($zoneId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'ad_zone')
					->where('zone_id = ?', $zoneId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Ad_Models_Zone($row);
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Zone::update()
	 */
	public function update($zone)
	{
		$this->_conn->update($this->_prefix . 'ad_zone',
							array(
								'name'   => $zone->name,
								'width'  => $zone->width,
								'height' => $zone->height,
							),
							array(
								'zone_id = ?' => $zone->zone_id,
							));
	}
}
