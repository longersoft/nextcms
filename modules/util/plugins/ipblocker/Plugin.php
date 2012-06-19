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
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Plugins_Ipblocker_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request) 
	{
		Core_Services_Db::connect('master');
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		if ($options == null) {
			return;
		}
		$ip		 = $request->getClientIp();
		$backend = Zend_Layout::getMvcInstance() ? ('admin' == Zend_Layout::getMvcInstance()->getLayout()) : false;
		$allowed = true;
		
		switch (true) {
			// Check if user is allowed to access the front-end
			case (!$backend && isset($options['frontend_ips']) && !empty($options['frontend_ips'])):
				$frontendIps = explode(',', $options['frontend_ips']);
				$allowed	 = !self::_isIpInRange($ip, $frontendIps);
				break;
			
			// Check if user is allowed to access the back-end
			case ($backend && isset($options['backend_ips']) && !empty($options['backend_ips'])):
				$backendIps	= explode(',', $options['backend_ips']);
				$allowed	= self::_isIpInRange($ip, $backendIps);
				break;
			default:
				break;
		}
		
		if (!$allowed) {
			$request->setModuleName('core')
					->setControllerName('Auth')
					->setActionName('deny')
					->setDispatched(true);
		}
	}
	
	/**
	 * Checks if the IP address belong to the IP ranges or not
	 *
	 * @param string $ip The IP address
	 * @param array $ipRanges Array of IP ranges. Each range can consist of "x" character
	 * For example: 192.167.1.x, 192.168.x.x, 192.169.1.1
	 * @return bool
	 */
	private static function _isIpInRange($ip, $ipRanges = array())
	{
		if (count($ipRanges) == 0) {
			return false;
		}
		
		foreach ($ipRanges as $range) {
			if ($ip == $range) {
				return true;
			}

			// Replace 'x' with (\d+)
			$range = '/' . str_replace('x', '(\d+)', $range) . '/';
			if (preg_match($range, $ip) == 1) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		$this->view->assign(array(
			'frontendIps' => ($options == null) ? '' : $options['frontend_ips'],
			'backendIps'  => ($options == null) ? '' : $options['backend_ips'],
		));
	}
	
	/**
	 * Saves the blocked IP addresses
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->_extension->getRequest();
		$result  = Core_Services_Plugin::setOptionsForInstance($this, array(
			'frontend_ips' => $request->getParam('frontend_ips'),
			'backend_ips'  => $request->getParam('backend_ips'),
		));
		return $result ? 'true' : 'false';
	}
}
