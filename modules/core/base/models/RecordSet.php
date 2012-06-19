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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Models_RecordSet implements Countable, Iterator, ArrayAccess 
{
	/**
	 * @var int
	 */
	protected $_count = 0;
	
	/**
	 * @var int
	 */
	private $_iteratorIndex = 0;
	
	/**
	 * @var Core_Base_Models_Gateway
	 */
	protected $_gateway;
	
	/**
	 * @var string
	 */
	protected $_entityClass;
	
	/**
	 * @var mixed
	 */
	protected $_results;
	
	public function __construct($results, $gateway, $entityClass = null) 
	{
		$this->_results 	= $results;
		$this->_gateway 	= $gateway;
		$this->_entityClass = $entityClass;
	}
	
	/**
	 * Uses this method if you want to convert the result set to JSON
	 * 
	 * @return array
	 */
	public function toArray()
	{
		if (!$this->_results) {
			return array();
		}
		$items = array();
		foreach ($this->_results as $item) {
			$item = $this->_gateway->convert($item);
			if ($item instanceof Core_Base_Models_Entity) {
				$items[] = $item->getProperties();
			}
		}
		return $items;
	}
	
	////////// IMPLEMENT COUNTABLE INTERFACE //////////
	
	/**
	 * @see Countable::count()
	 */
	public function count() 
	{
		if (null == $this->_count) {
			$this->_count = count($this->_results);
		}
		return $this->_count;
	}
	
	////////// IMPLEMENT ITERATOR INTERFACE //////////
	
	/**
	 * @see Iterator::key()
	 */
	public function key() 
	{
		return key($this->_results);	
	}
	
	/**
	 * @see Iterator::next()
	 */
	public function next() 
	{
		$this->_iteratorIndex++;
		return next($this->_results);
	}
	
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() 
	{
		$this->_iteratorIndex = 0;
		return reset($this->_results);
	}
	
	/**
	 * @see Iterator::valid()
	 */
	public function valid() 
	{
		return $this->_iteratorIndex < $this->count();
	}
	
	/**
	 * @see Iterator::current()
	 */
	public function current() 
	{
		$key    = ($this->_results instanceof Iterator)
					? $this->_results->key() 
					: key($this->_results);
		$result = $this->_results[$key];
//		if (get_class($result) != $this->_entityClass) {
			$result = $this->_gateway->convert($result);
			//$this->_results[$key] = $result;
//		}
		return $result;
	}
	
	////////// IMPLEMENT ARRAYACCESS INTERFACE //////////
	
	/**
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($key) 
	{
		return array_key_exists($key, $this->_results);
	}
	
	/**
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($key) 
	{
		return $this->_gateway->convert($this->_results[$key]);
    }
    
    /**
     * @see ArrayAccess::offsetSet()
     */
	public function offsetSet($key, $element) 
	{
		$this->_results[$key] = $element;
		$this->_count         = count($this->_results);
    }
    
    /**
     * @see ArrayAccess::offsetUnset()
     */
	public function offsetUnset($key) 
	{
		unset($this->_results[$key]);
		$this->_count = count($this->_results);
    }
}
