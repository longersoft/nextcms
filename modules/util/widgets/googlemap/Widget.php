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

class Util_Widgets_Googlemap_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('_format');
		switch ($format) {
			case 'json':
				$address = $request->getParam('address');
				return self::getLocation($address);
				break;
			default:
				$this->view->assign('uid', uniqid());
				break;
		}
	}
	
	/**
	 * Shows the Google map
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$request   = $this->getRequest();
		$address   = $request->getParam('address');
		$latitude  = $request->getParam('latitude');
		$longitude = $request->getParam('longitude');
		
		if (!$latitude || !$longitude) {
			$data = self::getLocation($address);
			if (isset($data['latitude']) && isset($data['longitude'])) {
				$latitude  = $data['latitude'];
				$longitude = $data['longitude'];
			}
		}
		
		$this->view->assign(array(
			'uid'		=> uniqid(),
			'address'	=> $address,
			'latitude'	=> $latitude,
			'longitude' => $longitude,
			'zoom'		=> $request->getParam('zoom', 14),
			'ajax'		=> $request->getParam('_ajax', false),
		));
	}
	
	/**
	 * Gets location of given address
	 * 
	 * @param string $address The address
	 * @return array
	 */
	public static function getLocation($address)
	{
		$url  = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' . urlencode($address);
		$data = file_get_contents($url);
		if (!$data) {
			return array();
		}
		$data = Zend_Json::decode($data);
		if ($data['status'] == 'OK') {
			$location = $data['results'][0]['geometry']['location'];
			return array(
				'latitude'  => $location['lat'],
				'longitude' => $location['lng'],
				'address'   => $data['results'][0]['formatted_address'],
			);
		} else {
			return array();
		}
	}
}
