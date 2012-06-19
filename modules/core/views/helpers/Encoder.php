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
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-26
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_Encoder
{
	/**
	 * Gets the view helper instance
	 * 
	 * @return Core_View_Helper_Encoder
	 */
	public function encoder()
	{
		return $this;
	}
	
	/**
	 * Encodes the properties of an entity. The result can be used as a property
	 * of HTML element, and can be decoded using Javascript
	 * 
	 * @param Core_Base_Models_Entity|array $entity Array or entity instance
	 * @param array $includeProperties Array of properties. If NULL, the method
	 * will encode all properties of the entity
	 * @return string
	 */
	public function encode($entity, $includeProperties = null)
	{
		if (!($entity instanceof Core_Base_Models_Entity)) {
			$entity = new Core_Base_Models_Entity($entity);
		}
		$items = $entity->getProperties($includeProperties);
		return $this->_encode($items);
	}
	
	/**
	 * Decodes an encoded string
	 * 
	 * @param string $string The encoded string
	 * @return array
	 */
	public function decode($string)
	{
		return Zend_Json::decode(base64_decode(rawurldecode($string)));
	}
	
	/**
	 * Encodes an array
	 * 
	 * @param array $array The array
	 * @return string
	 */
	private function _encode($array)
	{
		return rawurlencode(base64_encode(Zend_Json::encode($array)));
	}
}
