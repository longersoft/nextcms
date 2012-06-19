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
 * @version		2012-04-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Clazz
{
	/**
	 * Normalizes the class name
	 * 
	 * @param string $class The class name
	 * @return string
	 */
	public static function normalizeClass($class)
	{
		return str_replace(' ', '_', ucwords(str_replace('_', ' ', $class)));		
	}
}
