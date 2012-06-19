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
 * @subpackage	services
 * @since		1.0
 * @version		2012-02-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Captcha
{
	/**
	 * @var string
	 */
	const SESSION = 'Core_Services_Captcha';
	
	/**
	 * The default name of captcha form element
	 * 
	 * @var string
	 */
	const NAME = 'captcha';
	
	/**
	 * The default font of captcha
	 * 
	 * @var string
	 */
	const DEFAULT_CAPTCHA_FONT = '/modules/core/data/captcha.ttf';
	
	/**
	 * Gets captcha adapter
	 * 
	 * @param string $name The name of form element
	 * @return Zend_Captcha_Base
	 */
	public static function getCaptcha($name = self::NAME)
	{
		$adapter = Core_Services_Config::get('core', 'captcha_adapter', 'image');
		switch ($adapter) {
			case 'recaptcha':
				$options = Core_Services_Config::get('core', 'captcha_options');
				if (!$options) {
					throw new Exception('The public and private keys of ReCaptcha have not been set');
				}
				$options = Zend_Json::decode($options);
				$captcha = new Zend_Captcha_ReCaptcha();
				$captcha->setPubkey($options['recaptcha_public_key'])
						->setPrivkey($options['recaptcha_private_key']);
				return $captcha;
				break;
			case 'image':
			default:
				if (!file_exists(APP_TEMP_DIR . DS . 'captcha')) {
					// Create the directory for storing the generated captcha images
					Core_Base_File::createDirectories('captcha', APP_TEMP_DIR);
				}
				
				$view    = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
				$captcha = new Zend_Captcha_Image();
				$captcha->setFont(APP_ROOT_DIR . self::DEFAULT_CAPTCHA_FONT)
						->setImgDir(APP_TEMP_DIR . DS . 'captcha')
						->setImgUrl($view->APP_ROOT_URL . '/temp/captcha')
						->setSession(new Zend_Session_Namespace(self::SESSION))
						->setTimeout(300)
						->setName($name);
				return $captcha;
				break;
		}
	}
	
	/**
	 * Checks if a submited captcha is valid
	 * 
	 * @param Zend_Captcha_Base $captcha
	 * @param Zend_Controller_Request_Abstract $request The request
	 * @return bool
	 */
	public static function isValid($captcha, $request = null)
	{
		switch (true) {
			case ($captcha instanceof Zend_Captcha_ReCaptcha):
				return $captcha->isValid(array(
					'recaptcha_challenge_field' => $request->getParam('recaptcha_challenge_field'),
					'recaptcha_response_field'  => $request->getParam('recaptcha_response_field'),
				));
				break;
			case ($captcha instanceof Zend_Captcha_Image):
			default:
				$name = $captcha->getName() ? $captcha->getName() : self::NAME;
				return $captcha->isValid(array(
					'id'	=> $captcha->getId(),
					'input' => $request->getPost($name)
				));
				break;
		}
	}
}
