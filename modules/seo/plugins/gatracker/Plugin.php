<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		seo
 * @subpackage	plugins
 * @since		1.0
 * @version		2012-06-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Seo_Plugins_Gatracker_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::postDispatch()
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($request->isXmlHttpRequest()) {
			return;
		}
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		if ($options == null || !isset($options['code']) || !is_string($options['code']) || ('' == $options['code'])) {
			return;
		}
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->script()->appendScript(self::_generateCode($options['code']));
	}
	
	/**
	 * This method is called after installing the plugin from the back-end
	 * 
	 * @return void
	 */
	public function install()
	{
		// Clear the caching script
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->script()->cleanCaching();
	}
	
	/**
	 * This method is called after uninstalling the plugin from the back-end
	 * 
	 * @return void
	 */
	public function uninstall()
	{
		// Clear the caching script
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->script()->cleanCaching();
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
		$this->view->assign('code', ($options == null) ? '' : $options['code']);
	}
	
	/**
	 * Saves the tracking code
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->_extension->getRequest();
		$code	 = $request->getParam('code');
		$result	 = false;
		if ($code) {
			$result = Core_Services_Plugin::setOptionsForInstance($this, array('code' => $code));
		}
		return $result ? 'true' : 'false';
	}
	
	/**
	 * Generates Google Analytics tracking javascript code
	 * 
	 * @param string $code Tracking code (like GA-xxx)
	 * @return string
	 */
	private static function _generateCode($code)
	{
		if (!is_string($code) || $code == '') {
			return '';
		}
		return <<<END
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '$code']);
_gaq.push(['_trackPageview']);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();		
END;
	}
}
