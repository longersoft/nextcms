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
 * @version		2012-03-13
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_Paginator extends Zend_View_Helper_Abstract 
{
	/**
	 * Type of paginator. It can be one of the following values:
	 * - sliding
	 * - slidingToolbar
	 * - jumping
	 * 
	 * @var string
	 */
	private $_type		  = 'sliding';
	
	const SLIDING		  = 'sliding';
	const SLIDING_TOOLBAR = 'slidingToolbar';
	const JUMPING		  = 'jumping';
	
	/**
	 * Gets this view helper instance
	 * 
	 * @param string $type
	 * @return Core_View_Helper_Paginator
	 */
	public function paginator($type = 'sliding')
	{
		$this->_type = $type;
		return $this;
	}
	
	/**
	 * Renders the paginator
	 * 
	 * @param Zend_Paginator $paginator
	 * @param string $path The URL path that contains __PAGE__ string.
	 * The __PAGE__ string will be replaced with the page index
	 */
	public function render($paginator, $path)
	{
		// Don't show paginator if there's only one page
		if ($paginator->count() == 1) {
			// return '';
		}
		$this->view->addScriptPath(APP_ROOT_DIR . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'scripts');
		
		// The script that is used to render the paginator
		$script = '_base' . DS . '_paginator' . ucfirst($this->_type) .'.phtml';
		
		$translator  = $this->view->translator();
		$languageDir = $translator->getLanguageDir(); 
		$translator->setLanguageDir('/modules/core/languages');
		
		$output = $this->view->paginationControl($paginator,
												'Sliding', $script,
												array(
													'path' => $path,
												));
		// Reset the language dir
		$translator->setLanguageDir($languageDir);
		return $output;
	}
	
	/**
	 * Generates link to item
	 * 
	 * @param int $page Page index of item
	 * @param string $label Label of link
	 * @param string $path The URL path that contains __PAGE__ string.
	 * The __PAGE__ string will be replaced with the page index
	 * @return string
	 */
	public function renderItem($page, $label, $path)
	{
		$path = rtrim(ltrim($path));
		$url  = str_replace('__PAGE__', $page, $path);
		
		// 10 is length of "javascript" (without ")
		$isJsHandler = (0 == strncasecmp($path, 'javascript', 10));
		
		switch ($this->_type) {
			case self::JUMPING:
				// FIXME:
				break;
			
			case self::SLIDING_TOOLBAR:
				// 11 is length of "javascript:" (without ")
				return $isJsHandler
							? '<script type="dojo/method" event="onClick" args="e">' . "\n"
							. ltrim(substr($url, 11), ' ') . "\n"
							. '</script>' . "\n"
							: $url;
				break;
				
			case self::SLIDING:
			default:
				return sprintf('<a href="%s">%s</a>', $url, $label);
				break;
		}
	}
}
