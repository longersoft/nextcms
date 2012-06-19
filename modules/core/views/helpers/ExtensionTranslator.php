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

defined('APP_VALID_REQUEST') || die('Can not access script directly.');

class Core_View_Helper_ExtensionTranslator extends Zend_View_Helper_Abstract
{
	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 * @see Zend_View_Helper_Abstract::setView()
	 */
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
		return $this;
	}
	
	/**
	 * Gets the view helper instance
	 * 
	 * @return Core_View_Helper_ExtensionTranslator
	 */
	public function extensionTranslator()
	{
		return $this;
	}
	
	/**
	 * Translates the title of extension
	 * 
	 * @param Core_Models_Module|Core_Models_Hook|Core_Models_Plugin|Core_Models_Widget|Core_Models_Task $extension
	 * @return string
	 */
	public function translateTitle($extension)
	{
		return $this->_translate($extension, 'title');
	}
	
	/**
	 * Translates the description of extension
	 * 
	 * @param Core_Models_Module|Core_Models_Hook|Core_Models_Plugin|Core_Models_Widget|Core_Models_Task $extension
	 * @return string
	 */
	public function translateDescription($extension)
	{
		return $this->_translate($extension, 'description');
	}
	
	/**
	 * Translates title or description of extension
	 * 
	 * @param Core_Models_Module|Core_Models_Hook|Core_Models_Plugin|Core_Models_Widget|Core_Models_Task $extension
	 * @param string $key Can be "title" or "description"
	 * @return string
	 */
	private function _translate($extension, $key)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS;
		
		switch (true) {
			case ($extension instanceof Core_Models_Module):
				$file .= $extension->name . DS . 'configs' . DS . 'about.php';
				$dir   = '/modules/' . $extension->name . '/languages';
				break;
			case ($extension instanceof Core_Models_Hook):
				$file .= $extension->module . DS . 'hooks' . DS . $extension->name . DS . 'about.php';
				$dir   = '/modules/' . $extension->module . '/hooks/' . $extension->name;
				break;
			case ($extension instanceof Core_Models_Plugin):
				$file .= $extension->module . DS . 'plugins' . DS . $extension->name . DS . 'about.php';
				$dir   = '/modules/' . $extension->module . '/plugins/' . $extension->name;
				break;
			case ($extension instanceof Core_Models_Widget):
				$file .= $extension->module . DS . 'widgets' . DS . $extension->name . DS . 'about.php';
				$dir   = '/modules/' . $extension->module . '/widgets/' . $extension->name;
				break;
			case ($extension instanceof Core_Models_Task):
				$file .= $extension->module . DS . 'tasks' . DS . $extension->name . DS . 'about.php';
				$dir   = '/modules/' . $extension->module . '/tasks/' . $extension->name;
				break;
		}
		
		if (!file_exists($file)) {
			return $extension->$key;
		}
		$array = include $file;
		if (!is_array($array) || !isset($array[$key]['translationKey'])) {
			return $extension->$key;
		}
		
		$translationKey = $array[$key]['translationKey'];
		$return = $this->view->translator()->setLanguageDir($dir)->_($translationKey);
		
		// Reset the language dir
		$this->view->translator()->setLanguageDir(null);
		
		return $return;
	}
}
