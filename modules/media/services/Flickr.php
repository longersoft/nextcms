<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	services
 * @since		1.0
 * @version		2011-11-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Services_Flickr
{
	const SESSION_FLICKR   = 'SESSION_FLICKR';
	const FLICKR_PERMS	   = 'read';
	const FLICKR_REST_URI  = 'http://api.flickr.com';
	const FLICKR_REST_PATH = '/services/rest/';
	
	/**
	 * The Flickr API key
	 * 
	 * @var string
	 */
	private $_apiKey;
	
	/**
	 * The Flickr secret key
	 * 
	 * @var string
	 */
	private $_secretKey;
	
	/**
	 * The session stores Flickr user's Id
	 * 
	 * @var string
	 */
	private $_session;

	/**
	 * @var Media_Services_Flickr
	 */
	private static $_instance;
	
	/**
	 * Gets service instance
	 * 
	 * @return Media_Services_Flickr
	 */
	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Private constructor
	 * 
	 * @return void
	 */
	private function __construct()
	{
		$this->_apiKey	  = Core_Services_Config::get('media', 'flickr_api_key');
		$this->_secretKey = Core_Services_Config::get('media', 'flickr_secret_key');
		
		$this->_session   = new Zend_Session_Namespace(self::SESSION_FLICKR);
	}
	
	/**
	 * Gets the Flickr user's Id.
	 * Returns NULL if user has not authenticated
	 * 
	 * @return string
	 */
	public function getUserId()
	{
		return $this->_session->userId;
	}
	
	/**
	 * Gets the authentication URL
	 * 
	 * @see http://www.flickr.com/services/api/auth.howto.web.html
	 * @return string
	 */
	public function getAuthUrl()
	{
		$apiSignature = md5($this->_secretKey . 'api_key' . $this->_apiKey . 'perms' . self::FLICKR_PERMS);
		return 'http://www.flickr.com/services/auth/?api_key=' . $this->_apiKey 
				. '&perms=' . self::FLICKR_PERMS . '&api_sig='. $apiSignature;
	}
	
	/**
	 * Authenticates based on given frob
	 * 
	 * @see http://www.flickr.com/services/api/flickr.auth.getToken.html
	 * @param string $frob
	 * @return bool
	 */
	public function authenticate($frob)
	{
		// Get the authentication token
		$restClient = new Zend_Rest_Client(self::FLICKR_REST_URI);
		
		$params = array(
			'method' 		 => 'flickr.auth.getToken',
			'format' 		 => 'json',
			'nojsoncallback' => 1,
			'api_key' 		 => $this->_apiKey,
			'frob' 			 => $frob,
		);
		ksort($params);
		
		// Build API signature
		$apiSignature = '';
		foreach ($params as $key => $value) {
			$apiSignature .= $key . $value;
		}
		$apiSignature = md5($this->_secretKey . $apiSignature);
		
		$params['api_sig'] = $apiSignature;
		$body = $restClient->restGet(self::FLICKR_REST_PATH, $params)->getBody();
		$res  = Zend_Json::decode($body);
		
		if ($res['stat'] == 'fail') {
			return false;
		}
		
		$this->_session->userId = $res['auth']['user']['nsid'];
		$this->_session->token  = $res['auth']['token']['_content'];
		
		return true;
	}
	
	/**
	 * Gets the list of photos in given set
	 * 
	 * @see http://www.flickr.com/services/api/flickr.photosets.getPhotos.html
	 * @param string $setId The set's Id
	 * @return array
	 */
	public function getPhotos($setId)
	{
		$restClient = new Zend_Rest_Client(self::FLICKR_REST_URI);
		$body = $restClient->restGet(self::FLICKR_REST_PATH, array(
								'method' 		 => 'flickr.photosets.getPhotos',
								'format' 		 => 'json',
								'nojsoncallback' => 1,
								'api_key' 		 => $this->_apiKey,
								'photoset_id'	 => $setId,
								'privacy_filter' => 5,
//								'auth_token'     => $this->session->token,
						   ))
						   ->getBody();
		$res    = Zend_Json::decode($body);
		$photos = $res['photoset']['photo'];
		$return = array();
		
		for ($j = 0; $j < count($photos); $j++) {
			// See http://www.flickr.com/services/api/flickr.photos.getSizes.html
			$body = $restClient->restGet(self::FLICKR_REST_PATH, array(
								'method' 		 => 'flickr.photos.getSizes',
								'format' 		 => 'json',
								'nojsoncallback' => 1,
								'api_key' 		 => $this->_apiKey,
								'photo_id'	 	 => $photos[$j]['id'],
							   ))
							   ->getBody();
			$res    = Zend_Json::decode($body);
			$thumbs = $res['sizes']['size'];
			$urls   = array();
 			for ($k = 0; $k < count($thumbs); $k++) {
				$urls[strtolower($thumbs[$k]['label'])] = $thumbs[$k]['source'];
 			}
 			$return[] = array(
				'id'		 => $photos[$j]['id'],
 				'title'		 => $photos[$j]['title'],
				'thumbnails' => $urls,
			);
		}
		
		return $return;
	}
	
	/**
	 * Gets the list of Flickr sets
	 * 
	 * @see http://www.flickr.com/services/api/flickr.photosets.getList.html
	 * @param string $userId The Flickr user's Id
	 * @return array
	 */
	public function getSets($userId)
	{
		// See http://www.flickr.com/services/api/request.rest.html
		$restClient = new Zend_Rest_Client(self::FLICKR_REST_URI);
		
		// Get the list of sets
		$body = $restClient->restGet(self::FLICKR_REST_PATH, array(
								'method' 		 => 'flickr.photosets.getList',
								'format' 		 => 'json',
								'nojsoncallback' => 1,
								'api_key' 		 => $this->_apiKey,
								'user_id' 		 => $userId,
						   ))
						   ->getBody();
		$res  = Zend_Json::decode($body);
		$sets = $res['photosets']['photoset'];
		
		for ($i = 0; $i < count($sets); $i++) {
			// Get photo info
			// See http://www.flickr.com/services/api/flickr.photos.getInfo.html
			$body = $restClient->restGet(self::FLICKR_REST_PATH, array(
									'method' 		 => 'flickr.photos.getInfo',
									'format' 		 => 'json',
									'nojsoncallback' => 1,
									'api_key' 		 => $this->_apiKey,
									'photo_id' 		 => $sets[$i]['primary'],
							   ))
							   ->getBody();
			$res   = Zend_Json::decode($body);
			
			// Get set thumbnail
			$sets[$i]['thumb'] = 'http://farm' . $res['photo']['farm'] . '.static.flickr.com/' . $res['photo']['server'] . '/' . $res['photo']['id'] . '_' . $res['photo']['secret'] . '_s.jpg';
		}
		
		return $sets;
	}
}
