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
 * @version		2012-06-14
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Hooks_Urlshortener_Hook extends Core_Base_Extension_Hook
	implements Zend_Filter_Interface
{
	/**
	 * @see http://regexlib.com/REDetails.aspx?regexp_id=1048
	 * @var string
	 */
	const LINK_PATTERN = "~(?:https?://(?:(?:(?:(?:(?:[a-zA-Z\d](?:(?:[a-zA-Z\d]|-)*[a-zA-Z\d])?)\.)*(?:[a-zA-Z](?:(?:[a-zA-Z\d]|-)*[a-zA-Z\d])?))|(?:(?:\d+)(?:\.(?:\d+)){3}))(?::(?:\d+))?)(?:/(?:(?:(?:(?:[a-zA-Z\d$\-_.+!*'(),]|(?:%[a-fA-F\d]{2}))|[;:@&=])*)(?:/(?:(?:(?:[a-zA-Z\d$\-_.+!*'(),]|(?:%[a-fA-F\d]{2}))|[;:@&=])*))*)(?:\?(?:(?:(?:[a-zA-Z\d$\-_.+!*'(),]|(?:%[a-fA-F\d]{2}))|[;:@&=])*))?)?)~";
	
	/**
	 * Shortens links
	 * 
	 * @see Zend_Filter_Interface::filter()
	 * @param string $value The original content
	 * @return string
	 */
	public function filter($value)
	{
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Hook::getOptionsByInstance($this);
		$adapter = ($options && isset($options['adapter'])) ? $options['adapter'] : 'TinyUrlCom';
		$service = 'Zend_Service_ShortUrl_' . $adapter;
		$service = new $service();
		
		try {
			$doc = new DOMDocument();
			@$doc->loadHTML($value);
			$links = $doc->getElementsByTagName('a');
			if ($links) {
				foreach ($links as $linkNode) {
					$href = $linkNode->getAttribute('href');
					if ($href && ('http://' == substr($href, 0, 7) || 'https://' == substr($href, 0, 8))) {
						$node = $linkNode->cloneNode();
						$node->setAttribute('href', $service->shorten($href));
						// Replace the node with new one
						$linkNode->parentNode->replaceChild($node, $linkNode);
					}
				}
			}
			$value = $doc->saveXml($doc->getElementsByTagName('body')->item(0)->firstChild);
		} catch (Exception $ex) {
			if (preg_match_all(self::LINK_PATTERN, $value, $matches) !== false) {
				$replacements = array();
				foreach ($matches[0] as $url) {
					$replacements[$url] = $service->shorten($url);
				}
				$value = str_replace(array_keys($replacements), array_values($replacements), $value);
			}
		}
		
		return $value;
	}
	
	/**
	 * Shows configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Saves the hook's options
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$hook	 = Core_Services_Hook::getHookInstance('urlshortener', 'util');
		$options = array(
			'adapter' => $this->getRequest()->getParam('adapter', 'TinyUrlCom'),
		);
		$result = Core_Services_Hook::setOptionsForInstance($this, $options);
		return $result ? 'true' : 'false';
	}
}
