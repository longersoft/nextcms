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

class Core_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract 
{
	/**
	 * Shows flash messages
	 * 
	 * @param bool $showAsNotification If it is true, the view helper will show 
	 * the messages as notifications using the dojox.widget.Toaster widget.
	 * Otherwise, the flash messages will be listed in a DIV container.
	 * @return string
	 */
	public function flashMessenger($showAsNotification = true) 
	{
		$flashMsgHelper	= Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$this->view->addScriptPath(APP_ROOT_DIR . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'scripts');
		$this->view->assign(array(
			'showAsNotification' => $showAsNotification,
			'messages'			 => $flashMsgHelper->getMessages(),
		));
		
		return $this->view->render('_base/_flashMessenger.phtml');
	}
}
