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

class Core_View_Helper_HelperLoader extends Zend_View_Helper_Abstract
{
	/**
	 * Loads view helper from other module.
	 * In any view scripts, you can call a view helper provided by other module as follow:
	 *	<code>
	 *	$this->helperLoader('nameOfModule')->nameOfViewHelper(...);
	 *	</code>
	 *
	 * @param string $module Module name
	 * @return Zend_View_Abstract The view instance
	 */
	public function helperLoader($module)
	{
		$module = strtolower($module);
		$path	= APP_ROOT_DIR . DS . 'modules' . DS . $module . DS . 'views';
		$this->view->addHelperPath($path . DS . 'helpers', ucfirst($module) . '_View_Helper_');
		$this->view->addScriptPath($path . DS . 'scripts');
		return $this->view;
	}
}
