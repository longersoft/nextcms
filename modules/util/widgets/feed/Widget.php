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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Widgets_Feed_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Shows the latest RSS entries
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		$url	 = $request->getParam('url');
		if (!$url) {
			return;
		}
		$entries = Zend_Feed::import($url);
		$limit	 = $request->getParam('limit', count($entries));
		$title	 = $request->getParam('title', $entries->title());
		$index   = 0;
		$items   = array();
		foreach ($entries as $entry) {
			$index++;
			if ($index > $limit) {
				break;
			} else {
				$items[] = $entry;
			}
		}
		
		$this->view->assign(array(
			'entries' => $items,
			'title'	  => $title,
		));
	}
}
