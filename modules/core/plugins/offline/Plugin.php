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
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Offline_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @var array
	 */
	private static $_EXCEPT_ACTIONS = array(
		'core_auth_deny',
		'core_auth_login',
		'core_auth_logout',
	);
	
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$layout = Zend_Layout::getMvcInstance();
		if (!$layout || 'admin' == $layout->getLayout()) {
			return;
		}
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		if ($options && isset($options['message'])) {
			$message = $options['message'];
		} else {
			$view	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$message = $view->translator()->setLanguageDir('/modules/core/plugins/offline')->_('config.default');
			// Reset language dir
			$view->translator()->setLanguageDir(null);
		}
		$act = implode('_', array(
								$request->getModuleName(),
								$request->getControllerName(),
								$request->getActionName(),
							));
		$act = strtolower($act);
		if (in_array($act, self::$_EXCEPT_ACTIONS)) {
			return;
		}
		
		$request->setModuleName('core')
				->setControllerName('Notification')
				->setActionName('show')
				->setParam('message', $message)
				->setParam('appCurrentPage', null)
				->setDispatched(true);
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$this->view->assign('options', Core_Services_Plugin::getOptionsByInstance($this));
	}
	
	/**
	 * Saves the plugin's options
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		$request = $this->_extension->getRequest();
		$message = $request->getParam('message');
		$result  = Core_Services_Plugin::setOptionsForInstance($this, array('message' => $message));
		return $result ? 'true' : 'false';
	}
}
