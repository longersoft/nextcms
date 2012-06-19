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
 * @version		2011-12-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Controllers_Plugins_CronTask extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown()
	{
		$view	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		// Get the root URL which is defined in the Core_Controllers_Plugins_Init plugin
		$rootUrl = $view->APP_ROOT_URL;
		
		// Request to the cron script
		$cronScript = rtrim($rootUrl, '/') . '/cron.php';
		
		if (!empty($_POST)) {
			return;
		}
		
		try {
			// See http://framework.zend.com/manual/en/zend.http.client.html#zend.http.client.configuration
			$config  = array(
				'adapter'	 => 'Zend_Http_Client_Adapter_Socket',
				'timeout'	 => 10,
				'persistent' => true,
			);
			$request = new Zend_Http_Client($cronScript, $config);
			$request->setMethod(Zend_Http_Client::GET)
					->setParameterGet('image', 'false')
					->request();
		} catch (Exception $ex) {
			// FIXME: Log the error
		}
	}
}
