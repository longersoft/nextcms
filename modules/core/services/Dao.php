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
 * @version		2012-04-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Dao
{
	/**
	 * Database adapter
	 * 
	 * @var string
	 */
	protected static $_dbAdapter = null;
	
	/**
	 * Gets a DAO instance.
	 * For example, the following line returns the dao instance that implements Module interface 
	 * (Core_Dao_Models_Interface_Module):
	 * 		$moduleDao = Core_Services_Dao::factory(array(
	 * 							'name'   => 'module',
	 * 							'module' => 'core',
	 * 						));
	 * 
	 * @param array $options Contains the following members:
	 * - name: Name of DAO
	 * - module: Name of module
	 * @return Core_Base_Models_Dao
	 */
	public static function factory($options = array())
	{
		if (self::$_dbAdapter == null) {
			$config 		  = Core_Services_Config::getAppConfigs();
			self::$_dbAdapter = self::_normalizeAdapter($config['db']['adapter']);
		}
		if (!isset($options['adapter'])) {
			$options['adapter'] = self::$_dbAdapter;
		} else {
			$options['adapter'] = self::_normalizeAdapter($options['adapter']);	
		}
		
		switch (true) {
			case (isset($options['hook'])):
				$class = $options['module'] . '_Hooks_' . $options['hook'] . '_Models_Dao_Adapters_' . $options['adapter'] . '_' . $options['name'];
				break;
			case (isset($options['plugin'])):
				$class = $options['module'] . '_Plugins_' . $options['plugin'] . '_Models_Dao_Adapters_' . $options['adapter'] . '_' . $options['name'];
				break;
			case (isset($options['task'])):
				$class = $options['module'] . '_Tasks_' . $options['task'] . '_Models_Dao_Adapters_' . $options['adapter'] . '_' . $options['name'];
				break;
			case (isset($options['widget'])):
				$class = $options['module'] . '_Widgets_' . $options['widget'] . '_Models_Dao_Adapters_' . $options['adapter'] . '_' . $options['name'];
				break;
			default:
				$class = $options['module'] . '_Models_Dao_Adapters_' . $options['adapter'] . '_' . $options['name'];
				break;
		}
		$class = Core_Base_Clazz::normalizeClass($class);
		if (!class_exists($class)) {
			throw new Exception(sprintf('Can not find the class of %s', $class));
		}
		$obj = new $class();
		if (!$obj instanceof Core_Base_Models_Dao) {
			throw new Exception(sprintf('The instance of %s has to extend from Core_Base_Models_Dao', $class));
		}
		return $obj;
	}
	
	/**
	 * Normalizes the adapter
	 * 
	 * @param string $adapter
	 * @return string
	 */
	private static function _normalizeAdapter($adapter)
	{
		if ($adapter == 'mysql') {
			// It is possible to use the same APIs in MySQL adapter as in the Pdo_Mysql adapter
			$adapter = 'pdo_mysql';
		}
		return $adapter;
	}
}
