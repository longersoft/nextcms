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
 * @version		2012-03-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Cache 
{
	/**
	 * The site content (such as page, widget content) should be cached with this tag
	 * 
	 * @var string
	 */
	const TAG_SITE_CONTENT = 'TAG_SITE_CONTENT';
	
	/**
	 * Gets global cache instance
	 * 
	 * @return Zend_Cache_Core
	 */
	public static function getInstance() 
	{
		$config = Core_Services_Config::getAppConfigs();
		if (!isset($config['cache']) || !isset($config['cache']['frontend']) 
			|| !isset($config['cache']['backend'])) 
		{
			return null;
		}
		
		$frontendOptions = $config['cache']['frontend']['options'];
		$backendOptions  = $config['cache']['backend']['options'];
		$frontendOptions = self::_replaceConst($frontendOptions);
		$backendOptions  = self::_replaceConst($backendOptions);
		
		return Zend_Cache::factory($config['cache']['frontend']['name'], 
								   $config['cache']['backend']['name'],
								   $frontendOptions, $backendOptions);
	}
	
	/**
	 * @param array $options
	 * @return array
	 */
	private static function _replaceConst($options) 
	{
		$search		= array('{DS}', '{APP_TEMP_DIR}');
		$replace	= array(DS, APP_TEMP_DIR);
		$newOptions = array();
		foreach ($options as $key => $value) {
			$newOptions[$key] = str_replace($search, $replace, $value);
		}
		return $newOptions;
	}
}
