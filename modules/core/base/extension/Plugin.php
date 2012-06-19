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
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Extension_Plugin extends Core_Base_Extension
{
	public function __construct($name, $module)
	{
		parent::__construct($name, $module);
		
		$path		  = 'modules' . DS . $module . DS . 'plugins' . DS . $name;
		$template	  = Core_Services_Template::getCurrentTemplate();
		$templatePath = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'views' . DS . $module . DS . 'plugins' . DS . $name;
		
		$this->addHelperPath(APP_ROOT_DIR . DS . $path, $module . '_Plugins_' . $name . '_')
			 ->addScriptPath($templatePath)		// When render a plugin action, the app will look for the script in the template directory first
			 ->addScriptPath(APP_ROOT_DIR . DS . $path)
			 ->setLanguagePath($path);
	}
}
