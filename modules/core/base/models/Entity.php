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
 * @subpackage	base
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Models_Entity
{
	/**
	 * Entity's properties
	 * 
	 * @var array
	 */
	protected $_properties;
	
	public function __construct($data = array())
	{
		if (is_object($data)) {
			$data = (array) $data;
		}
		if (!is_array($data)) {
			//throw new Exception('The data must be an array or object');
		}
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
		
		return $this;
	}
	
	/**
	 * Gets the entity's properties
	 *
	 * @param array $properties Array of properties. If it is null, the method
	 * returns all the properties of entity
	 * @return array
	 */
	public function getProperties($properties = null)
	{
		$data = $this->_properties;
		
		// Unset the _id attribute if using MongoDB adapter
		if (class_exists('MongoId') && isset($data['_id']) && ($data['_id'] instanceof MongoId)) {
			unset($data['_id']);
		}
		if ($properties == null) {
			return $data;
		}
		$data = array();
		foreach ($properties as $name) {
			$data[$name] = isset($this->_properties[$name]) ? $this->_properties[$name] : null;
		}
		return $data;
	}
	
	public function __set($name, $value)
	{
		$this->_properties[$name] = $value;
	}
	
	public function __get($name) 
	{
		if (array_key_exists($name, $this->_properties)) {
			return $this->_properties[$name];
		}
		return null;
	}
	
	public function __isset($name) 
	{
		return isset($this->_properties[$name]);
	}
	
	public function __unset($name) 
	{
		if (isset($this->$name)) {
			$this->_properties[$name] = null;
		}
	}
	
	/**
	 * This helper method checks a value is null or empty
	 * 
	 * @param mixed $value
	 * @return bool
	 */
	public function isNullOrEmpty($value)
	{
		return ($value == null || empty($value));
	}
	
	/**
	 * Gets the Id of entity. It should be overrided in the entity class
	 * 
	 * @return string
	 */
	public function getId()
	{
		return '';
	}

	/**
	 * Gets the title of entity. It should be overrided in the entity class
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return '';
	}
	
	/**
	 * Sanitize properties. Each property will be sanitized by applying the Core_SanitizeInput filter
	 * 
	 * @param array $pros Array of properties
	 * @return Core_Base_Models_Entity
	 */
	public function sanitize($properties = array())
	{
		$hookRegistry = Core_Base_Hook_Registry::getInstance();
		if (count($properties) == 0) {
			$properties = $this->_properties;
		}
		
		foreach ($properties as $k) {
			if (isset($this->_properties[$k])) {
				$this->_properties[$k] = $hookRegistry->executeFilter('Core_SanitizeInput', $this->_properties[$k]);
			}
		}
		return $this;
	}
}
