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
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_OpenIdStorage extends Zend_OpenId_Consumer_Storage
{
	/**
	 * @see Zend_OpenId_Consumer_Storage::addAssociation()
	 */
	public function addAssociation($url, $handle, $macFunc, $secret, $expires)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->addAssociation($url, $handle, $macFunc, $secret, $expires);
	}

	/**
	 * @see Zend_OpenId_Consumer_Storage::getAssociation()
	 */
	public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->getAssociation($url, $handle, $macFunc, $secret, $expires);
	}
	
	/**
	 * @see Zend_OpenId_Consumer_Storage::getAssociationByHandle()
	 */
	public function getAssociationByHandle($handle, &$url, &$macFunc, &$secret, &$expires)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->getAssociationByHandle($handle, $url, $macFunc, $secret, $expires);
	}

	/**
	 * @see Zend_OpenId_Consumer_Storage::delAssociation()
	 */
	public function delAssociation($url)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->deleteAssociation($url);
	}
	
	/**
	 * @see Zend_OpenId_Consumer_Storage::addDiscoveryInfo()
	 */
	public function addDiscoveryInfo($id, $realId, $server, $version, $expires)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->addDiscoveryInfo($id, $realId, $server, $version, $expires);
	}
	
	/**
	 * @see Zend_OpenId_Consumer_Storage::getDiscoveryInfo()
	 */
	public function getDiscoveryInfo($id, &$realId, &$server, &$version, &$expires)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->getDiscoveryInfo($id, $realId, $server, $version, $expires);
	}
	
	/**
	 * @see Zend_OpenId_Consumer_Storage::delDiscoveryInfo()
	 */
	public function delDiscoveryInfo($id)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->deleteDiscoveryInfo($id);
	}
	
	/**
	 * @see Zend_OpenId_Consumer_Storage::isUniqueNonce()
	 */
	public function isUniqueNonce($provider, $nonce)
	{
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'core',
									'name'   => 'OpenIdStorage',
								))
								->setDbConnection($conn)
								->isUniqueNonce($provider, $nonce);
	}
	
	/**
	 * @see Zend_OpenId_Consumer_Storage::purgeNonces()
	 */
	public function purgeNonces($date = null)
	{
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'core',
							'name'   => 'OpenIdStorage',
						 ))
						 ->setDbConnection($conn)
						 ->purgeNonces($date);
	}
}
