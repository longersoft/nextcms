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
 * @version		2012-05-14
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Counter
{
	/**
	 * All requests from the same IP address or the same authenticated user
	 * are counted as only one in this lifetime (in seconds)
	 * 
	 * @var int
	 */
	private static $_defaultLifetime = 604800;		// Cache in 7 days
	
	/**
	 * @var string
	 */
	const CACHE_KEY_PREFIX = 'Core_Services_Counter';
	
	/**
	 * @var string
	 */
	const CACHE_TAG = 'Core_Services_Counter';
	
	/**
	 * Gets the cache instance
	 * 
	 * @return Zend_Cache_Core
	 */
	public static function getCache()
	{
		$cache = Core_Services_Cache::getInstance();
		if ($cache) {
			return $cache;
		} else {
			return Zend_Cache::factory('Core', 'File', array(
											'lifetime'				  => self::$_defaultLifetime,
											'automatic_serialization' => true,
											'cache_id_prefix'		  => 'app_counter_',
										), array(
											'cache_dir' => APP_TEMP_DIR . DS . 'cache',
										));
		}
	}
	
	/**
	 * Sets the default lifetime
	 * 
	 * @param int $lifetime
	 * @return void
	 */
	public static function setDefaultLifetime($lifetime)
	{
		self::$_defaultLifetime = $lifetime;
	}
	
	/**
	 * Registers a counter.
	 * For example, when user view an article, the number of article's views
	 * will be increased by one.
	 * Also, all the requests from same IP address or same authenticated user
	 * are counted as only once in a given time.
	 * 
	 * Sample usage:
	 * <code>
	 * Core_Services_Counter::register($article, 'views', 'Content_Services_Article::increaseNumViews', array($article), 900);
	 * </code>
	 * 
	 * @param Core_Base_Models_Entity $entity The entity instance
	 * @param string $key Contains only [a-zA-Z0-9_] because it will be used to generate
	 * the cache key. This parameter must be defined if you want to update different counters
	 * of the same entity
	 * @param string $callback The callback method that is used to update the counter
	 * @param array $arguments The callback arguments
	 * @param int $lifetime
	 * @return void
	 */
	public static function register($entity, $key = 'counter', $callback = null, $arguments = array(), $lifetime = null)
	{
		if ($entity == null || !($entity instanceof Core_Base_Models_Entity)) {
			return;
		}
		
		if ($lifetime == null) {
			$lifetime = self::$_defaultLifetime;
		}
		
		// Generate the cache key
		$cacheKey = self::_createCacheKey($entity, $key);
		
		// Check if the current counter is in cache or not
		$cache = self::getCache();
		$data  = $cache->load($cacheKey);
		if ($data === false) {
			$cache->save(true, $cacheKey, array(self::CACHE_TAG), $lifetime);
			
			// Execute the callback
			if ($callback && $arguments) {
				call_user_func_array($callback, $arguments);
			}
		}
	}
	
	/**
	 * Checks if a counter is registered or not
	 * 
	 * @param Core_Base_Models_Entity $entity The entity instance
	 * @param string $key
	 * @return bool
	 */
	public static function isRegistered($entity, $key = 'counter')
	{
		$cacheKey = self::_createCacheKey($entity, $key);
		$cache	  = self::getCache();
		if ($cache->test($cacheKey) === false) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Generates a key to cache the counter
	 * 
	 * @param Core_Base_Models_Entity $entity The entity instance
	 * @param string $key
	 * @return string
	 */
	private static function _createCacheKey($entity, $key = 'counter')
	{
		// Generate the cache key
		$cacheKey = array(self::CACHE_KEY_PREFIX, $key);
		
		// The key to control requests from the same IP address or from authenticated user
		if (Zend_Auth::getInstance()->hasIdentity()) {
			array_push($cacheKey, 'user', Zend_Auth::getInstance()->getIdentity()->user_id);
		} else {
			$ip = Zend_Controller_Front::getInstance()->getRequest()->getClientIp();
			// Zend_Cache just allows only [a-zA-Z0-9_] to be used in the cache key
			$ip = str_replace('.', 'x', $ip);
			array_push($cacheKey, 'ip', $ip);
		}
		array_push($cacheKey, get_class($entity), $entity->getId());
		$cacheKey = implode('_', $cacheKey);
		$cacheKey = md5($cacheKey);
		
		return $cacheKey;
	}
}
