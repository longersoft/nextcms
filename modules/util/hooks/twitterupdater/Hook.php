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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-04-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Hooks_Twitterupdater_Hook extends Core_Base_Extension_Hook
{
	/**
	 * Consumer key provided by Twitter
	 * 
	 * @var string
	 */
	const CONSUMER_KEY = '9h7KRBQm5qWJ8xhiTE6vg';
	
	/**
	 * Consumer secret key provided by Twitter
	 * 
	 * @var string
	 */
	const CONSUMER_SECRET_KEY = 'E6s0REAAqoM7L8WcNZZMixQoRuiElEwcqLqnvnkI';
	
	/**
	 * Default template of status
	 * 
	 * @var string
	 */
	const DEFAULT_TEMPLATE = '#title# #url#';
	
	/**
	 * Posts new message to Twitter
	 * 
	 * @param Core_Base_Models_Entity $entity The entity
	 * @param string $url The link of page showing the entity data
	 * @return bool
	 */
	public static function post($entity, $url)
	{
		$options = Core_Services_Hook::getOptions('twitterupdater', 'util');
		if (!$options || !isset($options['access_token']) || !isset($options['access_token_secret'])
			|| !$options['access_token'] || !$options['access_token_secret'])
		{
			return false;
		}
		if (!($entity instanceof Core_Base_Models_Entity)) {
			return false;
		}
		
		// Shorten the URL
		$service = isset($options['shorturl_service']) ? $options['shorturl_service'] : 'TinyUrlCom';
		$service = 'Zend_Service_ShortUrl_' . $service;
		$service = new $service();
		$url	 = $service->shorten($url);
		
		// Get status template
		$status = isset($options['status_template']) ? $options['status_template'] : self::DEFAULT_TEMPLATE;
		$status = str_replace('#url#', $url, $status);

		// Get the title of entity
		$length = 140 - str_replace('#title#', '', $status) - 2;
		$title  = $entity->getTitle();
		$title  = Core_Base_String::sub($title, $length, '');
		
		// Post new status to Twitter
		$status	= str_replace('#title#', $title, $status);
		
		$token  = new Zend_Oauth_Token_Access();
		$token->setToken($options['access_token'])
			  ->setTokenSecret($options['access_token_secret']);
		$twitter = new Zend_Service_Twitter(array(
			'consumerKey'	 => self::CONSUMER_KEY,
			'consumerSecret' => self::CONSUMER_SECRET_KEY,
			'accessToken'	 => $token
		));
		$response = $twitter->status->update($status);
		return $response->text ? true : false;
	}
	
	/**
	 * Shows configuration form to set the access token or authorize
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$options = Core_Services_Hook::getOptionsByInstance($this);
		$this->view->assign(array(
			'shortUrlService'	=> ($options && isset($options['shorturl_service'])) ? $options['shorturl_service'] : 'TinyUrlCom',
			'accessToken'		=> ($options && isset($options['access_token'])) ? $options['access_token'] : '',
			'accessTokenSecret' => ($options && isset($options['access_token_secret'])) ? $options['access_token_secret'] : '',
			'statusTemplate'	=> ($options && isset($options['status_template'])) ? $options['status_template'] : self::DEFAULT_TEMPLATE,
		));
	}
	
	/**
	 * Authorizes in Twitter
	 * 
	 * @return void
	 */
	public function authorizeAction()
	{
		$config = array(
		    'callbackUrl'	 => $this->view->serverUrl() . $this->view->url(array(), 'core_extension_render') . '?' . http_build_query(array(
									'_type'	  => 'hook',
									'_mod'	  => 'util',
									'_name'	  => 'twitterupdater',
									'_method' => 'authorizeAction',
									'step'	  => 'done',
								)),
		    'siteUrl'		 => Zend_Service_Twitter::OAUTH_BASE_URI,
			'authorizeUrl'	 => 'https://api.twitter.com/oauth/authenticate',
		    'consumerKey'	 => self::CONSUMER_KEY,
		    'consumerSecret' => self::CONSUMER_SECRET_KEY,
		);
		$consumer = new Zend_Oauth_Consumer($config);
		$request  = $this->getRequest();
		$step	  = $request->getParam('step');
		$session  = new Zend_Session_Namespace('Util_Hooks_TwitterUpdater_Hook');
		
		switch ($step) {
			case 'auth':
				$token = $consumer->getRequestToken();
				$session->requestToken = serialize($token);
				$consumer->redirect();
				exit();
				break;
			case 'done':
				if (!empty($_GET) && isset($session->requestToken)) {
					$token = $consumer->getAccessToken($_GET, unserialize($session->requestToken));
					$session->accessToken = serialize($token);
					unset($session->requestToken);
				}
				$accessToken = isset($session->accessToken) ? unserialize($session->accessToken) : null;
				$this->view->assign(array(
					'accessToken'		=> $accessToken ? $accessToken->getToken() : null,
					'accessTokenSecret' => $accessToken ? $accessToken->getTokenSecret() : null,
				));
				break;
		}
	}
	
	/**
	 * Saves the access token
	 * 
	 * @return void
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		// TODO: Encode the tokens
		$options = array(
			'shorturl_service'	  => $request->getParam('shorturl_service'),
			'access_token'		  => $request->getParam('access_token'),
			'access_token_secret' => $request->getParam('access_token_secret'),
			'status_template'	  => $request->getParam('status_template'),
		);
		if ($options['access_token'] && $options['access_token_secret']) {
			$result = Core_Services_Hook::setOptionsForInstance($this, $options);
		} else {
			$result = false;
		}
		return $result ? 'true' : 'false';
	}
}
