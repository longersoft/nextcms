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
 * @version		2012-03-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_Models_Dao_Adapters_Pdo_Mysql_Banner extends Core_Base_Models_Dao
	implements Ad_Models_Dao_Interface_Banner
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return new Ad_Models_Banner($entity);
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::add()
	 */
	public function add($banner)
	{
		$this->_conn->insert($this->_prefix . 'ad_banner',
							array(
								'title'		   => $banner->title,
								'format'	   => $banner->format,
								'code'		   => $banner->code,
								'target'	   => $banner->target,
								'target_url'   => $banner->target_url,
								'url'		   => $banner->url,
								'status'	   => $banner->status,
								'created_date' => $banner->created_date,
								'from_date'	   => $banner->from_date,
								'to_date'	   => $banner->to_date,
							));
		$bannerId = $this->_conn->lastInsertId($this->_prefix . 'ad_banner');
		
		// Add the banner-page associations
		if ($banner->pages) {
			foreach ($banner->pages as $item) {
				$this->_conn->insert($this->_prefix . 'ad_banner_page_assoc',
									array(
										'banner_id' => $bannerId,
										'zone_id'   => $item['zone_id'],
										'page_id'   => null,
										'ordering'  => 0,
										'url'		=> $item['url'],
									));
			}
		}
		
		return $bannerId;
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::addAssociationPage()
	 */
	public function addAssociationPage($page, $banners)
	{
		$this->_conn->delete($this->_prefix . 'ad_banner_page_assoc',
							array(
								'page_id = ?' => $page->page_id,
							));
		foreach ($banners as $item) {
			$this->_conn->insert($this->_prefix . 'ad_banner_page_assoc',
								array(
									'banner_id' => $item['banner_id'],
									'zone_id'   => $item['zone_id'],
									'page_id'   => $page->page_id,
									'ordering'  => $item['ordering'],
									'url'		=> null,
								));
		}
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::count()
	 */
	public function count($criteria = array())
	{
		$select = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'ad_banner', array('num_banners' => 'COUNT(*)'));
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('status = ?', $criteria['status']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$select->where("(title LIKE '%" . addslashes($criteria['keyword']) . "%')");
		}
		return $select->limit(1)->query()->fetch()->num_banners;
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::delete()
	 */
	public function delete($banner)
	{
		$this->_conn->delete($this->_prefix . 'ad_banner',
							array(
								'banner_id = ?' => $banner->banner_id,
							));
		$this->_conn->delete($this->_prefix . 'ad_banner_page_assoc',
							array(
								'banner_id = ?' => $banner->banner_id,
							));
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::find()
	 */
	public function find($criteria = array(), $offset = null, $count = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('b' => $this->_prefix . 'ad_banner'));
		if (isset($criteria['status']) && !empty($criteria['status'])) {
			$select->where('b.status = ?', $criteria['status']);
		}
		if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
			$select->where("(b.title LIKE '%" . addslashes($criteria['keyword']) . "%')");
		}
		
		if (!isset($criteria['sort_by'])) {
			$criteria['sort_by'] = 'b.banner_id';
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
	 * @see Ad_Models_Dao_Interface_Banner::getAssociationBanners()
	 */
	public function getAssociationBanners($page)
	{
		$result = $this->_conn
					   ->select()
					   ->from(array('b' => $this->_prefix . 'ad_banner'))
					   ->joinInner(array('bp' => $this->_prefix . 'ad_banner_page_assoc'), 'b.banner_id = bp.banner_id', array('zone_id', 'page_id', 'ordering'))
					   ->where('bp.page_id = ?', $page->page_id)
					   ->query()
					   ->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::getBannersInZone()
	 */
	public function getBannersInZone($zone, $page = null)
	{
		$select = $this->_conn
					   ->select()
					   ->from(array('b' => $this->_prefix . 'ad_banner'))
					   ->joinInner(array('bp' => $this->_prefix . 'ad_banner_page_assoc'), 'b.banner_id = bp.banner_id', array('zone_id', 'page_id', 'banner_url' => 'url', 'ordering'))
					   ->joinLeft(array('p' => $this->_prefix . 'core_page'), 'bp.page_id = p.page_id', array('route', 'template', 'language', 'page_url' => 'url'))
					   ->where('bp.zone_id = ?', $zone->zone_id)
					   ->where('b.status = ?', Ad_Models_Banner::STATUS_ACTIVATED)
					   ->order('ordering ASC');
		if ($page) {
			$select->where('p.route = ?', $page->route)
				   ->where('p.template = ?', $page->template)
				   ->where('p.language = ?', $page->language);
		}
		$result = $select->query()->fetchAll();
		return new Core_Base_Models_RecordSet($result, $this);		   
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::getById()
	 */
	public function getById($bannerId)
	{
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'ad_banner')
					->where('banner_id = ?', $bannerId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Ad_Models_Banner($row);	
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::getStaticLinks()
	 */
	public function getStaticLinks($banner)
	{
		$result = $this->_conn
					   ->select()
					   ->from($this->_prefix . 'ad_banner_page_assoc')
					   ->where('banner_id = ?', $banner->banner_id)
					   ->where('page_id IS NULL')
					   ->query()
					   ->fetchAll();
		$pages  = array();
		foreach ($result as $row) {
			$pages[] = array(
				'banner_id' => $row->banner_id,
				'zone_id'	=> $row->zone_id,
				'page_id'   => $row->page_id,
				'ordering'  => $row->ordering,
				'url'		=> $row->url,
			);
		}
		return $pages;
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::update()
	 */
	public function update($banner)
	{
		$this->_conn->update($this->_prefix . 'ad_banner',
							array(
								'title'		   => $banner->title,
								'format'	   => $banner->format,
								'code'		   => $banner->code,
								'target'	   => $banner->target,
								'target_url'   => $banner->target_url,
								'url'		   => $banner->url,
								'status'	   => $banner->status,
								'created_date' => $banner->created_date,
								'from_date'	   => $banner->from_date,
								'to_date'	   => $banner->to_date,
							),
							array(
								'banner_id = ?' => $banner->banner_id,
							));
		// Update the association with pages
		if ($banner->pages) {
			$this->_conn->delete($this->_prefix . 'ad_banner_page_assoc',
								array(
									'banner_id = ?' => $banner->banner_id,
									'page_id IS NULL',
								));
			foreach ($banner->pages as $item) {
				$this->_conn->insert($this->_prefix . 'ad_banner_page_assoc',
									array(
										'banner_id' => $banner->banner_id,
										'zone_id'   => $item['zone_id'],
										'page_id'   => null,
										'ordering'  => 0,
										'url'		=> $item['url'],
									));
			}
		}
	}
	
	/**
	 * @see Ad_Models_Dao_Interface_Banner::updateStatus()
	 */
	public function updateStatus($banner)
	{
		$this->_conn->update($this->_prefix . 'ad_banner',
							array(
								'status' => $banner->status,
							),
							array(
								'banner_id = ?' => $banner->banner_id,
							));
	}
}
