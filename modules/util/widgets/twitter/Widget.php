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
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Widgets_Twitter_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
	}
	
	/**
	 * Shows the Twitter messages
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request  = $this->getRequest();
		$limit	  = $request->getParam('limit', 10);
		$searchBy = $request->getParam('search_by');
		$messages = array();
		$username = null;
		
		switch ($searchBy) {
			case 'tag':
				$tag		   = $request->getParam('tag');
				$languageCode  = $request->getParam('language_code');
				$twitterSearch = new Zend_Service_Twitter_Search('json');
				$messages	   = $languageCode
								? $twitterSearch->search($tag, array('lang' => $languageCode))
								: $twitterSearch->search($tag);
				if ($messages && isset($messages['results'])) {
					$messages = $messages['results'];
				}
				break;
			case 'username':
				$username = $request->getParam('username');
				// Zend_Service_Twitter requires access token:
				// 		$twitter  = new Zend_Service_Twitter(array(
				//			'username' => $username,
				//		));
				//		$messages = $twitter->status->publicTimeline();
				//
				// So I have to request to
				// http://twitter.com/statuses/user_timeline/{$account}.json to
				// get the pubic message
				$url   = 'http://twitter.com/statuses/user_timeline/' . $username . '.json';
				$items = file_get_contents($url);
				if ($items) {
					$items = Zend_Json::decode($items);
					foreach ($items as $message) {
						$message['from_user'] = $username;
						$messages[] = $message;
					}
				}
				break;
			default:
				break;
		}
		if ($messages) {
			$messages = array_slice($messages, 0, $limit);
		}
		$this->view->assign(array(
			'title'	   => $request->getParam('title', ''),
			'username' => $username,
			'messages' => $messages,
		));
	}
}
