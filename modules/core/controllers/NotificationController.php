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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-06-13
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_NotificationController extends Zend_Controller_Action
{
	/**
	 * Logs the errors
	 * 
	 * @return void
	 */
	public function logAction()
	{
		$request = $this->getRequest();
		$error 	 = $request->getParam('error_handler');
		$class   = get_class($error->exception);
        $message = $error->exception->getMessage();
        
        // Check if the app is installed or not
        $config = Core_Services_Config::getAppConfigs();
        if (isset($config['install']['version'])) {
        	Core_Services_Db::connect('master');
        	
        	// Log the error
        	Core_Services_Error::add(new Core_Models_Error(array(
        		'created_user' => Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->user_id : null,
				'created_date' => date('Y-m-d H:i:s'),
				'uri'		   => $request->getRequestUri(),
				'module'	   => ($request->module == null) ? $request->getModuleName() : $request->module,
				'controller'   => ($request->controller == null) ? $request->getControllerName() : $request->controller,
				'action'	   => ($request->action == null) ? $request->getActionName() : $request->action,
				'class'		   => $class,
				'file'		   => $error->exception->getFile(),
				'line'		   => $error->exception->getLine(),
				'message'	   => $message,
				'trace'		   => $error->exception->getTraceAsString(),
        	)));
        }
        
        if ($request->isXmlHttpRequest()) {
        	$this->_helper->json(array(
        		'result'  => 'APP_RESULT_ERROR',
        		'message' => $message,
        	));
        } else {
        	switch ($class) {
        		case 'Core_Base_Exception_NotFound':
        			$this->getResponse()->setHttpResponseCode(404);
        			break;
        		case 'Zend_Controller_Router_Exception':
        		case 'Zend_Controller_Action_Exception':
        			if (404 == $error->exception->getCode()) {
        				$this->getResponse()->setHttpResponseCode(404);
        				// Show a friendly message of 404 Not Found error
        				$message = $this->view->translator()->_('global._share.error404');
        			}
        			break;
        		case 'Zend_Controller_Dispatcher_Exception':
        			$this->getResponse()->setHttpResponseCode(404);
        			// Show a friendly message of 404 Not Found error
        			$message = $this->view->translator()->_('global._share.error404');
        			break;
        		default:
        			break;
        	}
        	
        	$request->setParam('message', $message)
        			->setParam('appCurrentPage', null);
        	$this->_forward('show');
        }
	}
	
	/**
	 * Shows the message (offline, error message, ...)
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		
		// Set the title of page
		$config = Core_Services_Config::getAppConfigs();
        if (isset($config['install']['version'])) {
        	Core_Services_Db::connect('slave');
        	if ($title = Core_Services_Config::get('core', 'site_title')) {
        		$this->view->headTitle()->append($title);
        	}
        }
		
		$this->view->assign('message', $request->getParam('message'));
	}
}
