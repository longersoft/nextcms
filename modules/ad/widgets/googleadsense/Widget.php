<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-05-24
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_Widgets_Googleadsense_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Shows the banners
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		$this->view->assign('code', $request->getParam('code'));
	}
}
