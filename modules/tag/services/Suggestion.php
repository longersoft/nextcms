<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	services
 * @since		1.0
 * @version		2012-01-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_Services_Suggestion
{
	/**
	 * Suggests tags for given input string
	 * 
	 * @param string $content The input
	 * @return array
	 */
	public static function suggest($content)
	{
		$content = self::_format($content);
		$tags    = array();
		if (Core_Services_Config::get('tag', 'yql_enabled', 'false') == 'true') {
			$tags = array_merge($tags, self::suggestByYql($content));
		}
		if (Core_Services_Config::get('tag', 'opencalais_enabled', 'false') == 'true'
			&& ($apiKey = Core_Services_Config::get('tag', 'opencalais_api_key', ''))) 
		{
			$tags = array_merge($tags, self::suggestByOpenCalais($content, $apiKey));
		}
		return $tags;
	}
	
	/**
	 * Gets tags suggested by Yahoo Query Language service
	 * 
	 * @param string $content The input
	 * @return array
	 */
	public static function suggestByYql($content)
	{
		$content = str_replace('"', '', $content);
		$content = str_replace("\n", '', $content);
		$query	 = "SELECT * FROM search.termextract WHERE context = '" . addslashes($content) . "'";
		
		// I cannot use Zend_Rest_Client or Zend_Http_Client to get the response data
		try {
			$url = 'http://query.yahooapis.com/v1/public/yql?q=' . urlencode($query);
			$xml = simplexml_load_file($url);
			if ($xml->results->Result) {
				$body = $xml->results->Result;
				$suggestions = array();
				foreach ($body as $key => $value) {
					$suggestions[strtolower((string) $value)] = (string) $value;
				}
				return $suggestions;
			}
		} catch (Exception $ex) {
		}
		return array();
	}
	
	/**
	 * Gets tags suggested by OpenCalais service
	 * 
	 * @param string $content The input
	 * @param string $apiKey The API key provided by OpenCalais
	 * @return array
	 */
	public static function suggestByOpenCalais($content, $apiKey)
	{
		$suggestions = array();
		
		// See http://framework.zend.com/manual/en/zend.http.client.html#zend.http.client.configuration
		// http://www.opencalais.com/documentation/calais-web-service-api/api-invocation/rest
		try {
			$client = new Zend_Http_Client();
			$client->setUri('http://api.opencalais.com/tag/rs/enrich')
				   ->setHeaders(array(
				   		'x-calais-licenseID' => $apiKey,
				   		'Content-Type'		 => 'text/xml; charset=UTF-8',
				   		'Accept'			 => 'application/json',
				   ))
				   ->setParameterPost('content', $content)
				   ->setParameterPost('outputFormat', 'application/json')
				   ->setConfig(array(
						'adapter'	 => 'Zend_Http_Client_Adapter_Socket',
						'timeout'	 => 10,
						'persistent' => true,
				   ))
				   ->setMethod(Zend_Http_Client::POST);
			$response = $client->request();
			$body	  = $response->getBody();
			$body	  = Zend_Json::decode($body);
			
			foreach ($body as $key => $value) {
				if (isset($value['name']) && $value['name']) {
					$suggestions[strtolower($value['name'])] = $value['name'];
				}
			}
		} catch (Exception $ex) {
		}
		return $suggestions;
	}
	
	/**
	 * Formats the input
	 * 
	 * @param string $content The input
	 * @return string
	 */
	private static function _format($content)
	{
		$content = strip_tags($content);
		return $content;
	}
}
