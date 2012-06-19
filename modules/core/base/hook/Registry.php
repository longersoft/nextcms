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
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Hook_Registry 
{
	/**
	 * @var string
	 */
	const HOOKS = 'Core_Base_Hook_Registry';
	
	/**
	 * @var Core_Base_Hook_Registry
	 */
	private static $_instance;
	
	/**
	 * @var array
	 */
	private static $_hooks;
	
	/**
	 * @return Core_Base_Hook_Registry
	 */
	public static function getInstance()
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @return void
	 */
	private function __construct() 
	{
		self::$_hooks = array();
	}
	
	/**
	 * Register an action or a filter
	 * 
	 * @param string $name Name of action or filter
	 * @param array $hook The hook. It can be one of the following one:
	 * 1) 'functionName'
	 * 2) $obj
	 * 3) array('functionName')
	 * 	  array('functionName', $data)
	 * If we want to call static method, then the function name have to be
	 * formated as: 
	 * 	  objectClass::methodName
	 * 4) array($obj)
	 * 5) array($obj, 'methodName')
	 *    array($obj, 'methodName', $data)
	 * @param int $priority Hook priority
	 * @param bool $echo If TRUE, it will echo when calling the hook method
	 * @return Core_Base_Hook_Registry
	 */
	public function register($name, $hook = array(), $echo = false, $priority = 10)
	{
		$key = $this->_buildKeyId($hook);
		self::$_hooks[$name][$priority][$key] = array(
			'hook' => $hook,
			'echo' => $echo,
		);
		
		return $this;
	}
	
	/**
	 * Unregister an action or filter
	 * 
	 * @param string $name
	 * @param array $hook
	 * @param int $priority
	 * @return bool
	 */
	public function unregister($name, $hook = array(), $priority = 10) 
	{
		$key   = $this->_buildKeyId($hook);
		$isset = isset(self::$_hooks[$name][$priority][$key]);
		if (true === $isset) {
			unset(self::$_hooks[$name][$priority][$key]);
			if (empty(self::$_hooks[$name][$priority])) {
				unset(self::$_hooks[$name][$priority]);
			}
		}
		return $isset;
	}
	
	/**
	 * @param string $name
	 * @param int $priority
	 * @return bool
	 */
	public function isRegistered($name, $priority = 10) 
	{
		$isset = isset(self::$_hooks[$name][$priority]);
		return $isset;
	}
	
	/**
	 * Unregister all actions or filters
	 * 
	 * @param string $name
	 * @param int $priority
	 * @return Core_Base_Hook_Registry
	 */
	public function unregisterAll($name, $priority = false) 
	{
		if (isset(self::$_hooks[$name])) {
			if (false !== $priority && isset(self::$_hooks[$name][$priority])) {
				unset(self::$_hooks[$name][$priority]);
			} else {
				unset(self::$_hooks[$name]);
			}
		}	
		return $this;
	}
	
	/**
	 * Runs filter
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @param array $args
	 * @return mixed
	 */
	public function executeFilter($name, $value, $args = array()) 
	{
		$hooks = self::$_hooks;
		
		if (!array_key_exists($name, $hooks)) {
			return $value;
		}
		if (!is_array($hooks[$name])) {
			throw new Exception('There is not hook for ' . $name ."\n");
			return $value;
		}
		
//		$args = array();
//		if (empty($args)) {
//			$args = func_get_args();
//		}
		// Sort by the priority
		ksort($hooks[$name]);
		
		foreach ($hooks[$name] as $index => $hookArray) {
			foreach ($hookArray as $key => $hookData) {
				$hook = $hookData['hook'];
				
				// $hook can be one of following formats:
				// 1) 'functionName'
				// 2) $obj
				// 3) array('functionName')
				//    array('functionName', $data)
				// If we want to call static method, then the function name have to be
				// formated as: objectClass::methodName
				// 4) array($obj)
				// 5) array($obj, 'methodName')
				//	  array($obj, 'methodName', $data)
				$object   = null;
				$method   = null;
				$func 	  = null;
				$data 	  = null;
				$haveData = false;
				
				// Case 3, 4, 5
				if (is_array($hook)) {
					if (count($hook) < 1) {
						throw new Exception('Empty array in hooks for ' . $name . "\n");
					}
					// Case 4, 5
					else if (is_object($hook[0])) {
						$object = $hook[0];
						// Case 4
						if (count($hook) < 2) {
							$method = 'on' . $name;
						}
						// Case 5 
						else {
							$method = $hook[1];
							if (count($hook) > 2) {
								$data	  = $hook[2];
								$haveData = true;
							}
						}
					}
					// Case 3
					else if (is_string($hook[0])) {
						$func = $hook[0];
						if (count($hook) > 1) {
							$data	  = $hook[1];
							$haveData = true;
						}
					} else {
						throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
					}
				}
				// Case 1
				else if (is_string($hook)) {
					$func = $hook;
				}
				// Case 2
				else if (is_object($hook)) {
					$object = $hook;
					$method = "on" . $name;
				} else {
					throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
				}
				
				// Call method and pass variables
				if (!is_array($args)) {
					$args = array($args);
				}
				
				$hookArgs = $haveData ? array_merge(array($data), $args) : $args;
				
				if (isset($object)) {
					$func	  = get_class($object) . '::' . $method;
					$callback = array($object, $method);
				} elseif (false !== ($pos = strpos( $func, '::' ))) {
					$callback = array(substr($func, 0, $pos), substr($func, $pos + 2));
				} else {
					$callback = $func;
				}
				
				if (is_callable($callback)) {
					array_unshift($hookArgs, $value);
					$value = call_user_func_array($callback, $hookArgs);
				}
			}
		}
		
		return $value;
	}
	
	/**
	 * Runs action
	 * 
	 * @param string $name
	 * @param array $args
	 * @return void
	 */
	public function executeAction($name, $args = array()) 
	{
		$hooks = self::$_hooks;
		if (!array_key_exists($name, $hooks)) {
			return;
		}
		if (!is_array($hooks[$name])) {
			throw new Exception('There is not hook for ' . $name ."\n");
			return;
		}
		
		// Sort by the priority
		ksort($hooks[$name]);
		
		foreach ($hooks[$name] as $index => $hookArray) {
			foreach ($hookArray as $key => $hookData) {
				$hook = $hookData['hook'];
				
				// $hook can be one of following formats:
				// 1) 'functionName'
				// 2) $obj
				// 3) array('functionName')
				//    array('functionName', $data)
				// If we want to call static method, then the function name have to be
				// formated as: objectClass::methodName
				// 4) array($obj)
				// 5) array($obj, 'methodName')
				//    array($obj, 'methodName', $data) 
				$object   = null;
				$method   = null;
				$func	  = null;
				$data	  = null;
				$haveData = false;
				
				// Case 3, 4, 5
				if (is_array($hook)) {
					if (count($hook) < 1) {
						throw new Exception('Empty array in hooks for ' . $name . "\n");
					}
					// Case 4, 5
					else if (is_object($hook[0])) {
						$object = $hook[0];
						// Case 4
						if (count($hook) < 2) {
							$method = 'on' . $name;
						}
						// Case 5
						else {
							$method = $hook[1];
							if (count($hook) > 2) {
								$data	  = $hook[2];
								$haveData = true;
							}
						}
					}
					// Case 3
					else if (is_string($hook[0])) {
						$func = $hook[0];
						if (count($hook) > 1) {
							$data	  = $hook[1];
							$haveData = true;
						}
					} else {
						throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
					}
				}
				// Case 1 
				else if (is_string($hook)) {
					$func = $hook;
				}
				// Case 2 
				else if (is_object($hook)) {
					$object = $hook;
					$method = "on" . $name;
				} else {
					throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
				}
				
				// Call method and pass variables
				if (!is_array($args)) {
					$args = array($args);
				}
				//$hookArgs = $haveData ? array_merge(array($data), $args) : $args;
				$hookArgs = $haveData ? array_merge($data, $args) : $args;
				
				if (isset($object)) {
					$func	  = get_class($object) . '::' . $method;
					$callback = array($object, $method);
				} elseif (false !== ($pos = strpos( $func, '::' ))) {
					$callback = array(substr($func, 0, $pos), substr($func, $pos + 2));
				} else {
					$callback = $func;
				}
				
				if (is_callable($callback)) {
					if ($hookData['echo']) {
						echo call_user_func_array($callback, $hookArgs);
					} else {
						call_user_func_array($callback, $hookArgs);
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getHooks()
	{
		if (!Zend_Registry::isRegistered(self::HOOKS) 
			|| null == Zend_Registry::get(self::HOOKS)
		) {
			Zend_Registry::set(self::HOOKS, self::$_hooks);	
		}
		return Zend_Registry::get(self::HOOKS);
	}
	
	/**
	 * @param array $hook
	 * @return string
	 */
	private function _buildKeyId($hook = array())
	{
		// string
		if (is_string($hook)) {
			return $hook;
		}
		// object
		if (is_object($hook[0])) {
			//return get_class($hook[0]).$hook[1];
			return get_class($hook[0]) . '__' . $hook[1] . uniqid();
		}
		// Static method
		else if (is_string($hook[0])) {
			return $hook[0] . $hook[1];
		}
	}
}
