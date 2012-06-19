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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Models_Dao_Adapters_Pdo_Mysql_OpenIdStorage extends Core_Base_Models_Dao
	implements Core_Models_Dao_Interface_OpenIdStorage
{
	/**
	 * @see Core_Base_Models_Dao::convert()
	 */
	public function convert($entity)
	{
		return $entity;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::addAssociation()
	 */
	public function addAssociation($url, $handle, $macFunc, $secret, $expires)
	{
		$secret = base64_encode($secret);
		$this->_conn->insert($this->_prefix . 'core_openid_assoc', array(
			'url'	   => $url,
			'handle'   => $handle,
			'mac_func' => $macFunc,
			'secret'   => $secret,
			'expires'  => $expires,
		));
		return true;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::getAssociation()
	 */
	public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
	{
		$this->_conn->delete($this->_prefix . 'core_openid_assoc',
							array(
								'expires < ?' => time(),
							));
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_openid_assoc', array('handle', 'mac_func', 'secret', 'expires'))
					->where('url = ?', $url)
					->query()
					->fetch();
		if ($row != null) {
			$handle  = $row->handle;
			$macFunc = $row->mac_func;
			$secret  = base64_decode($row->secret);
			$expires = $row->expires;
			return true;
		}
		return false;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::getAssociationByHandle()
	 */
	public function getAssociationByHandle($handle, &$url, &$macFunc, &$secret, &$expires)
	{
		$this->_conn->delete($this->_prefix . 'core_openid_assoc',
							array(
								'expires < ?' => time(),
							));
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_openid_assoc', array('url', 'mac_func', 'secret', 'expires'))
					->where('handle = ?', $handle)
					->query()
					->fetch();
		if ($row != null) {
			$url     = $row->url;
			$macFunc = $row->mac_func;
			$secret  = base64_decode($row->secret);
			$expires = $row->expires;
			return true;
		}
		return false;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::deleteAssociation()
	 */
	public function deleteAssociation($url)
	{
		$this->_conn->delete($this->_prefix . 'core_openid_assoc',
							array(
								'url = ?' => $url,
							));
		return true;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::addDiscoveryInfo()
	 */
	public function addDiscoveryInfo($id, $realId, $server, $version, $expires)
	{
		$this->_conn->insert($this->_prefix . 'core_openid_discovery', array(
			'discovery_id' => $id,
			'real_id'	   => $realId,
			'server'	   => $server,
			'version'	   => $version,
			'expires'	   => $expires,
		));
		return true;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::getDiscoveryInfo()
	 */
	public function getDiscoveryInfo($id, &$realId, &$server, &$version, &$expires)
	{
		$this->_conn->delete($this->_prefix . 'core_openid_discovery',
							array(
								'expires < ?' => time(),
							));
		$row = $this->_conn
					->select()
					->from($this->_prefix . 'core_openid_discovery', array('real_id', 'server', 'version', 'expires'))
					->where('discovery_id = ?', $id)
					->query()
					->fetch();
		if ($row != null) {
			$realId  = $row->real_id;
			$server  = $row->server;
			$version = $row->version;
			$expires = $row->expires;
			return true;
		}
		return false;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::deleteDiscoveryInfo()
	 */
	public function deleteDiscoveryInfo($id)
	{
		$this->_conn->delete($this->_prefix . 'core_openid_discovery',
							array(
								'discovery_id = ?' => $id,
							));
		return true;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::isUniqueNonce()
	 */
	public function isUniqueNonce($provider, $nonce)
	{
		try {
			$this->_conn->insert($this->_prefix . 'core_openid_nonce',
								array(
									'nonce'   => $nonce,
									'created' => time(),
								));
		} catch (Zend_Db_Statement_Exception $e) {
			return false;
		}
		return true;
	}
	
	/**
	 * @see Core_Models_Dao_Interface_OpenIdStorage::purgeNonces()
	 */
	public function purgeNonces($date = null)
	{
		// FIXME
	}
}
