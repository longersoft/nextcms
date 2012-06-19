<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		util
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Hooks_Navigator_Hook extends Core_Base_Extension_Hook
	implements Zend_Filter_Interface
{
	/**
	 * @var array
	 */
	private $_headingIds = array();
	
	/**
	 * @var int
	 */
	private $_headingCount = 0;
	
	/**
	 * Creates table of content
	 * 
	 * @see Zend_Filter_Interface::filter()
	 * @param string $value The original content
	 * @return string
	 */
	public function filter($value)
	{
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Hook::getOptionsByInstance($this);
		$depth   = ($options && isset($options['depth'])) ? $options['depth'] : 6;
		$toc	 = '';
		
		// Return if there is no heading
		$found = preg_match('/<h([2-' . $depth . ']*)([^>]*)>(.*)?<\/h[2-' . $depth . ']>/', $value);
		if ($found === false || $found == 0) {
			return $value;
		}
		
		$value   = preg_replace_callback('/<h([2-' . $depth . ']*)([^>]*)>(.*)?<\/h[2-' . $depth . ']>/', array($this, '_formatHeading'), $value);
		
		if (preg_match_all('/<h[2-' . $depth . ']*([^>]*) id="(.*)"><a href="#(.*)">(.*)<\/a><\/h[2-' . $depth . ']>/', $value, $matches) !== false) {
			$toc = '<div class="utilHooksNavigator">';
			
			// Display the header (Table of contents)
			$header = $this->view->translator()->_('filter.header');
			$toc   .= '<div class="utilHooksNavigatorHeader">' . $header . '<span class="utilHooksNavigatorToggle">[-]</span></div>';
			
			$heading = implode('', $matches[0]);
			$heading = preg_replace('/<h([1-' . $depth . '])([^>]*)>/', '<li class="utilHooksNavigatorLevel$1">', $heading);
			$heading = preg_replace('/<\/h[1-' . $depth . ']>/', '</li>', $heading);
			
			$toc .= '<ul class="utilHooksNavigatorToc">'
				 . $heading
				 . '</ul>'
				 . '</div>';
		}
		
		return $toc . '<div class="utilHooksNavigatorTarget">' . $value . '</div>' . $this->show();
	}
	
	/**
	 * Attaches the script to toogle the table of content
	 * 
	 * @return void
	 */
	public function showAction()
	{
	}
	
	/**
	 * Formats the headings found in the content.
	 * 
	 * @param array $matches
	 * @return string
	 */
	private function _formatHeading($matches)
	{
		$heading = $matches[1];
		$attrs	 = $matches[2];
		$content = $matches[3];
		
		$id = Core_Base_String::clean(strip_tags($content));
		if (!in_array($id, $this->_headingIds)) {
			$this->_headingIds[] = $id;
		} else {
			$this->_headingCount++;
			$id .= '-' . $this->_headingCount;
		}
		
		return '<h' . $heading . ($attrs ? ' ' . $attrs : '') . ' id="' . $id . '">'
				. '<a href="#' . $id . '">' . $content . '</a>'
				. '</h' . $heading . '>';
	}
}
