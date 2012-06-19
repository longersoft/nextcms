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

class Seo_Plugins_Verification_Plugin extends Core_Base_Controllers_Plugin
{
	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Plugin::getOptionsByInstance($this);
		if (!$options) {
			return;
		}
		
		// Get the view instance
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		// Show the meta tag named "google-site-verification"
		if (isset($options['google_code']) && !empty($options['google_code'])) {
			$view->headMeta()->setName('google-site-verification', $options['google_code']);
		}
		// and "msvalidate.01"
		if (isset($options['bing_code']) && !empty($options['bing_code'])) {
			$view->headMeta()->setName('msvalidate.01', $options['bing_code']);
		}
		// and "alexaVerifyID"
		if (isset($options['alexa_code']) && !empty($options['alexa_code'])) {
			$view->headMeta()->setName('alexaVerifyID', $options['alexa_code']);
		}
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
			'googleCode' => ($options == null || !isset($options['google_code'])) ? '' : $options['google_code'],
			'bingCode'	 => ($options == null || !isset($options['bing_code'])) ? '' : $options['bing_code'],
			'alexaCode'  => ($options == null || !isset($options['alexa_code'])) ? '' : $options['alexa_code'],
		));
	}
	
	/**
	 * Saves the verification code
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		$request	= $this->_extension->getRequest();
		$googleCode	= $request->getParam('google_code');
		$bingCode   = $request->getParam('bing_code');
		$alexaCode	= $request->getParam('alexa_code');
		$result		= false;
		if ($googleCode || $bingCode || $alexaCode) {
			Core_Services_Db::connect('master');
			$result = Core_Services_Plugin::setOptionsForInstance($this, array(
				'google_code' => $googleCode,
				'bing_code'	  => $bingCode,
				'alexa_code'  => $alexaCode,
			));
		}
		return $result ? 'true' : 'false';
	}
}
