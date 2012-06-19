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

class Core_Controllers_Actions_Helpers_Csrf extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * @var string
	 */
	const TOKEN_ELEMENT_VAR = 'tokenElement';
	
	/**
	 * @var string
	 */
	protected $_salt = 'salt';
	
	/**
	 * @var string
	 */
	protected $_name = 'csrf';
	
	/**
	 * @var int
	 */
	protected $_timeout = 300;

	/**
	 * @var string
	 */
	protected $_session = null;
	
	/**
	 * @var bool
	 */
	protected $_csrfEnable = false;
	
	/**
	 * CSRF request method to attack site. It can take value of POST or GET
	 * 
	 * @var string
	 */
	protected $_csrfRequestMethod = 'POST';
	
	/**
	 * Defines where to get the taken value from. It can take value of POST or GET
	 * 
	 * @var string
	 */
	protected $_csrfRetriveMethod = 'POST';
	
	/**
	 * @var string
	 */
	protected $_token;
	
	public function __construct(array $config = array())
	{
		if (isset($config['salt'])) {
			$this->_salt = $config['salt']; 
		}
		if (isset($config['name'])) {
			$this->_name = $config['name'];
		}
		if (isset($config['timeout'])) {
			$this->_timeout = $config['timeout'];
		}
	}
	
	/**
	 * @see Zend_Controller_Action_Helper_Abstract::init()
	 */
	public function init()
	{
		// Do NOT continue processing if there is any error
		$request	  = $this->getRequest();
		$errorHandler = $request->getParam('error_handler'); 
		if ($errorHandler && $errorHandler->exception) {
			return;
		}
		
		$router = $this->getFrontController()->getRouter();
		$route  = $router->getCurrentRoute();
		
		if ($route instanceof Zend_Controller_Router_Route_Chain) {
			return;
		}
		
		$defaults = $route->getDefaults();
		if (isset($defaults['csrf']) && $defaults['csrf']['enabled']) {
			$this->_csrfEnable		  = true;
			$this->_csrfRequestMethod = strtoupper($defaults['csrf']['request']);
			$this->_csrfRetriveMethod = strtoupper($defaults['csrf']['retrive']);
		}
	}
	
	/**
	 * @see Zend_Controller_Action_Helper_Abstract::preDispatch()
	 */
	public function preDispatch()
	{
		if ($this->_csrfEnable) {
			$session = $this->_getSession();
			$session->setExpirationSeconds($this->_timeout);
			 
			$this->_token   = $session->token;
			$session->token = $this->_generateToken();
			
			$request = $this->getRequest();
			$isValid = null;
			
			if (($request->isPost() && $this->_csrfRequestMethod == 'POST')
				|| ($request->isGet() && $this->_csrfRequestMethod == 'GET')) 
			{
				switch ($this->_csrfRetriveMethod) {
					case 'POST':
						$token = $request->getPost($this->_name);
						break;
					case 'GET':
						$token = $request->getQuery($this->_name);
						break;
				}
				$isValid = $this->isValidToken($token);
			}
			
			if ($isValid === false) {
				throw new RuntimeException('Token does not match');
			}
		}
	}
	
	/**
	 * @see Zend_Controller_Action_Helper_Abstract::postDispatch()
	 */
	public function postDispatch()
	{
		if ($this->_csrfEnable) {
			$element = sprintf('<input type="hidden" name="%s" value="%s" />',
				$this->_name,
				$this->getToken()
			);
			
			$this->getActionController()->view->assign(self::TOKEN_ELEMENT_VAR, $element);
		}
	}
	
	/**
	 * @return string
	 */
	public function getTokenName() 
	{
		return $this->_name;
	}
	
	/**
	 * @param string $token
	 * @return bool
	 */
	public function isValidToken($token)
	{
		if (null == $token || '' == $token) {
			return false;
		}
		return ($token == $this->_token);
	}
	
	/**
	 * @return string
	 */
	public function getToken()
	{
		$session = $this->_getSession();
		if (!isset($session->token)) {
			// I need to regenerate token
			 $session->token = $this->_generateToken();
		}
		return $session->token;
	}	
	
	/**
	 * @return Zend_Session_Namespace
	 */
	private function _getSession()
	{
		if (null == $this->_session) {
			$this->_session = new Zend_Session_Namespace($this->_getSessionName());
		}
		return $this->_session;
	}
	
	/**
	 * @return string
	 */
	private function _getSessionName() 
	{
		return __CLASS__ . $this->_salt . $this->_name;
	}
	
	/**
	 * @return string
	 */
	private function _generateToken()
	{
		$token = md5(
			mt_rand(1, 1000000)
			. $this->_salt
			. $this->_name
			. mt_rand(1, 1000000)
		);
		return $token;
	}
}
