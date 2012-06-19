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

class Core_Plugins_Debug_Plugins_Memory extends Zend_Controller_Plugin_Abstract 
	implements Core_Plugins_Debug_Plugins_Interface
{
	/**
	 * @var bool
	 */
	protected $_supported = true;
	
	/**
	 * @var array
	 */
	protected $_memory = array(
		'preDispatch'  => 0,
		'postDispatch' => 0,
	);
	
	public function __construct()
	{
		$this->_supported = function_exists('memory_get_peak_usage');
		Zend_Controller_Front::getInstance()->registerPlugin($this);
	}
	
	/**
	 * @see Core_Plugins_Debug_Plugins_Interface::getData()
	 */
	public function getData()
	{
		if (!$this->_supported) {
			return null;
		}
		return array(
			'limit'		 => ini_get('memory_limit'),
			'usage'		 => memory_get_peak_usage(),
			'controller' => round(($this->_memory['postDispatch'] - $this->_memory['preDispatch']) / 1024, 2),
		);
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_supported) {
			$this->_memory['preDispatch'] = memory_get_peak_usage();
		}
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_supported) {
			$this->_memory['postDispatch'] = memory_get_peak_usage();
		}
	}
}
