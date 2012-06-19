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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Debug_Plugins_Registry implements Core_Plugins_Debug_Plugins_Interface
{
	/**
	 * @see Core_Plugins_Debug_Plugins_Interface::getData()
	 */
	public function getData()
	{
		$registry = Zend_Registry::getInstance();
		$registry->ksort();
		return array(
			'registry' => $registry,
		);
	}
}
