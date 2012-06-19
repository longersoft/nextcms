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
 * @version		2012-02-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Session_MemcacheHandler implements Zend_Session_SaveHandler_Interface
{
	/**
	 * @var Core_Services_Session_MemcacheHandler
	 */
	private static $_instance;
	
	/**
	 * Session lifetime
	 * @var int
	 */
	private $_lifetime;	
	
	/**
	 * @var Zend_Cache_Core
	 */
	private $_cache;
	
	private function __construct()
	{
		$config = Core_Services_Config::getAppConfigs();
		if (!isset($config['session_cache'])) {
			throw new Exception('The memcache options are not set');
		}
		
		$frontendOptions = $config['session_cache']['frontend']['options'];
		$backendOptions  = $config['session_cache']['backend']['options'];
		$this->_cache	 = Zend_Cache::factory($config['session_cache']['frontend']['name'], 
											   $config['session_cache']['backend']['name'],
											   $frontendOptions, $backendOptions);
											   
		$this->_lifetime = Core_Services_Config::get('core', 'session_memcache_lifetime', 
													(int) ini_get('session.gc_maxlifetime'));
	}
	
	public function __destruct()
	{
		Zend_Session::writeClose();
	}
	
	/**
	 * @return Core_Services_Session_MemcacheHandler
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::close()
	 */
	public function close()
	{
		return true;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::destroy()
	 */
	public function destroy($id)
	{
		return $this->_cache->remove($id);
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::gc()
	 */
	public function gc($maxlifetime)
	{
		return true;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::open()
	 */
	public function open($save_path, $name)
	{
		return true;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::read()
	 */
	public function read($id)
	{
		$return = '';
		$array	= $this->_cache->load($id);
		if (is_array($array) && $array[1]) {
			$return = $array[0];
			$this->_lifetime = $array[1];
		} else {
			$this->destroy($id);
		}
		
		return $return;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::write()
	 */
	public function write($id, $data)
	{
		return $this->_cache->save(array($data, $this->_lifetime), $id, array(), $this->_lifetime);
	}
}
