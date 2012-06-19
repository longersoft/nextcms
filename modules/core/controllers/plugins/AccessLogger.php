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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Controllers_Plugins_AccessLogger extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::postDispatch()
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		Core_Services_Db::connect('master');
		
		$logEnabled = Core_Services_Config::get('core', 'accesslog_enabled', 'false') == 'true';
		if ($logEnabled) {
			$view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$title = $view->headTitle();
			
			// 15 is length of <title></title>
			if ($title && strlen($title) >= 15) {
				$title = substr($title, 7, strlen($title) - 15);
			}
			
			// Log the access
			$accessLog = new Core_Models_AccessLog(array(
				'user_id'		=> Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->user_id : null,
				'title'			=> $title,
				'url'			=> $request->getRequestUri(),
				'module'		=> ($request->module == null) ? $request->getModuleName() : $request->module,
				'ip'			=> $request->getClientIp(),
				'accessed_date' => date('Y-m-d H:i:s'),
				'params'		=> Zend_Json::encode($request->getParams()),
			));
			Core_Services_AccessLog::add($accessLog);
		}
	}
}
