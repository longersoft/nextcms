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

class Core_View_Helper_RuleTranslator extends Zend_View_Helper_Abstract
{
	// ZF LESSON: The following section allows me to use the view instance
	
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
	 * @return Core_View_Helper_RuleTranslator
	 */
	public function ruleTranslator()
	{
		return $this;
	}
	
	/**
	 * Translates the description of resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @return string
	 */
	public function translateResource($resource)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $resource->module_name . DS . 'configs' . DS . 'permissions.php';
		if (!file_exists($file)) {
			return $resource->description;
		}
		$array = include $file;
		if (!is_array($array) || !isset($array[$resource->controller_name]['translationKey'])) {
			return $resource->description;
		}
		
		$translationKey = $array[$resource->controller_name]['translationKey'];
		$translation	= $this->view->translator()->setLanguageDir('/modules/' . $resource->module_name . '/languages')->_($translationKey);
		
		// Reset the language dir
		$this->view->translator()->setLanguageDir(null);
		
		return $translation;
	}
	
	/**
	 * Translates the description of privilege
	 * 
	 * @param Core_Models_Privilege $privilege
	 * @return string
	 */
	public function translatePrivilege($privilege)
	{
		$file = APP_ROOT_DIR . DS . 'modules' . DS . $privilege->module_name . DS . 'configs' . DS . 'permissions.php';
		if (!file_exists($file)) {
			return $privilege->description;
		}
		$array = include $file;
		if (!is_array($array) || !isset($array[$privilege->controller_name]['actions'][$privilege->action_name]['translationKey'])) {
			return $privilege->description;
		}
		
		$translationKey = $array[$privilege->controller_name]['actions'][$privilege->action_name]['translationKey'];
		$translation	= $this->view->translator()->setLanguageDir('/modules/' . $privilege->module_name . '/languages')->_($translationKey);
		
		// Reset the language dir
		$this->view->translator()->setLanguageDir(null);
		
		return $translation;
	}
}
