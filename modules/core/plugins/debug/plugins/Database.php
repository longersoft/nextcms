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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Debug_Plugins_Database implements Core_Plugins_Debug_Plugins_Interface
{
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $_adapter = null;

	public function __construct()
	{
		$adapter = Core_Services_Db::getConnection();
		if ($adapter instanceof Zend_Db_Adapter_Abstract) {
			// Enable the profile
			$adapter->getProfiler()->setEnabled(true);
			$this->_adapter = $adapter;
		}
	}
	
	/**
	 * @see Core_Plugins_Debug_Plugins_Interface::getData()
	 */
	public function getData()
	{
		if (!$this->_adapter) {
			return array();
		}
		
		$profiler = $this->_adapter->getProfiler();
		$data = array(
			'num_queries' => $profiler->getTotalNumQueries(),
			'total_time'  => $profiler->getTotalElapsedSecs(),
			'queries'	  => array(),
		);
		if ($profiles = $profiler->getQueryProfiles()) {
			foreach ($profiles as $profile) {
				$data['queries'][] = array(
					'query' => $profile->getQuery(),
					'time'  => $profile->getElapsedSecs(), 
				);
			}
		}
		return $data;
	}
}
