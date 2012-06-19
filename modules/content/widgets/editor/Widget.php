<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-05-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Widgets_Editor_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$request = $this->getRequest();
		$this->view->assign(array(
			'content'  => $request->getParam('content'),
			'editorId' => 'Content_Widgets_Editor_Widget_' . uniqid(),
		));
	}
	
	/**
	 * Shows the editor
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		$this->view->assign(array(
			'title'	  => $request->getParam('title', ''),
			'content' => $request->getParam('content', ''),
		));
	}
}
