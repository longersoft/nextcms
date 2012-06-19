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

interface Core_Models_Dao_Interface_OpenIdStorage
{
	public function addAssociation($url, $handle, $macFunc, $secret, $expires);
	
	public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires);
	
	public function getAssociationByHandle($handle, &$url, &$macFunc, &$secret, &$expires);
	
	public function deleteAssociation($url);
	
	public function addDiscoveryInfo($id, $realId, $server, $version, $expires);
	
	public function getDiscoveryInfo($id, &$realId, &$server, &$version, &$expires);
	
	public function deleteDiscoveryInfo($id);
	
	public function isUniqueNonce($provider, $nonce);
	
	public function purgeNonces($date = null);
}
