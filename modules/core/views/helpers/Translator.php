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
 * @version		2012-04-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_Translator extends Zend_View_Helper_Abstract
{
	/**
	 * @var string
	 */
	private $_language;
	
	/**
	 * @var bool
	 */
	private $_quote = false;
	
	/**
	 * The language directory
	 * 
	 * @var string
	 */
	private $_languageDir;

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
	}
	
	/**
	 * @param bool $quote If TRUE, the translatation string will be quoted with
	 * slashes
	 * @return Core_View_Helper_Translator
	 */
	public function translator($quote = false)
	{
		$this->_quote = $quote;
		return $this;
	}
	
	/**
	 * Sets the language manually
	 * 
	 * @param string $lang
	 * @return Core_View_Helper_Translator
	 */
	public function setLanguage($language)
	{
		$this->_language = $language;
		return $this;
	}
	
	/**
	 * Gets the current language
	 * 
	 * @return string
	 */
	public function getLanguage()
	{
		if ($this->_language == null) {
			$this->_language = $this->view->APP_LANGUAGE;
		}
		return $this->_language;
	}
	
	/**
	 * Sets the language directory
	 * 
	 * @param string|null $dir
	 * @return Core_View_Helper_Translator
	 */
	public function setLanguageDir($dir)
	{
		if ($dir) {
			$dir = str_replace('/', DS, $dir);
			$dir = ltrim($dir, DS);
			$dir = rtrim($dir, DS);
		}
		$this->_languageDir = $dir;
		return $this;
	}
	
	/**
	 * Gets the language directory
	 * 
	 * @return string
	 */
	public function getLanguageDir()
	{
		if ($this->_languageDir == null) {
			// I want to load the language file from current module
			$this->_languageDir = 'modules' . DS . Zend_Controller_Front::getInstance()->getRequest()->getModuleName() 
							 	. DS . 'languages';
		}
		return $this->_languageDir;
	}
	
	/**
	 * Translates the given translation key
	 * 
	 * @param string $key
	 * @param string $default If this parameter is supplied and there is no the language item associating with $key,
	 * 						  then the method will return the $default value
	 * @return string
	 */
	public function _($key, $default = null)
	{
		$file = APP_ROOT_DIR . DS . $this->getLanguageDir() . DS . $this->getLanguage() . '.json';
		if (!file_exists($file)) {
			return $key;
		}
		$content = file_get_contents($file);
		$return  = $key;
		if (!empty($content)) {
			$translate = new Zend_Translate('Core_Base_Translate_Adapters_Json', $file, $this->getLanguage());
			$item      = $translate->_($key);
			$return	   = ($item == $key && $default != null) ? $default : $item;
		}
		
		$return = $this->_quote ? addslashes($return) : $return;

		// Reset quote flag
		$this->_quote = false;
		
		return $return;
	}
}
