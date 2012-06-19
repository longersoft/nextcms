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
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Hooks_Slideshow_Hook extends Core_Base_Extension_Hook
	implements Zend_Filter_Interface
{
	public function __construct()
	{
		parent::__construct();
		if (!Zend_Layout::getMvcInstance() || Zend_Layout::getMvcInstance()->getLayout() != 'admin') {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/templates/default/js/jquery.colorbox.css')
								->appendStylesheet($this->view->APP_STATIC_URL . '/templates/' . $this->view->APP_TEMPLATE . '/skins/' . $this->view->APP_SKIN . '/util.hooks.slideshow.css');
		}
	}
	
	/**
	 * Shows a slideshow whenever user click on any image found in the content
	 * 
	 * @see Zend_Filter_Interface::filter()
	 * @param string $value The original content
	 * @return string
	 */
	public function filter($value)
	{
		$slide = $this->show();
		return $value . $slide;
	}
	
	/**
	 * Shows slideshow scripts
	 * 
	 * @return void
	 */
	public function showAction()
	{
	}
}
