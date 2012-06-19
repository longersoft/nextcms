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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_PrivilegeLoader extends Zend_View_Helper_Abstract
{
	/**
	 * Gets all privileges of given module
	 * 
	 * @param string $module
	 * @return array
	 */
	public function privilegeLoader($module)
	{
		Core_Services_Db::connect('master');
		return Core_Services_Privilege::getPrivileges($module);
	}
}
