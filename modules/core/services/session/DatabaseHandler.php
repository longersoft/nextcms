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
 * @version		2012-02-25
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Session_DatabaseHandler implements Zend_Session_SaveHandler_Interface 
{
	/**
	 * @var Core_Services_Session_DatabaseHandler
	 */
	private static $_instance;
	
	/**
	 * Session lifetime
	 * @var int
	 */
	private $_lifetime;
	
	/**
	 * @var Core_Models_Dao_Interface_Session
	 */
	private $_sessionDao;
	
	private function __construct()
	{
		$conn = Core_Services_Db::connect('master');
		
		$this->_lifetime   = Core_Services_Config::get('core', 'session_cookie_lifetime', 
														  (int) ini_get('session.gc_maxlifetime'));
		$this->_sessionDao = Core_Services_Dao::factory(array(
													'module' => 'core',
													'name'   => 'Session',
											  ))
											  ->setDbConnection($conn);
	}
	
	/**
	 * Add this destructor to fix the error: 
	 * "sqlsrv_errors contains an invalid type in ..." when use Sqlsrv adapter
	 * 
	 * @return void
	 */
	public function __destruct()
	{
		session_write_close();
	}
	
	/**
	 * @return Core_Services_Session_DatabaseHandler
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::close()
	 */
	public function close() 
	{
		return true;
	}

	/**
	 * @see Zend_Session_SaveHandler_Interface::destroy()
	 */
	public function destroy($id) 
	{
		return $this->_sessionDao->delete($id);
	}

	/**
	 * @see Zend_Session_SaveHandler_Interface::gc()
	 */
	public function gc($maxlifetime) 
	{
		$this->_sessionDao->destroy(time());
		return true;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::open()
	 */
	public function open($save_path, $name) 
	{
		return true;	
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::read()
	 */
	public function read($id) 
	{
		$return  = '';
		$session = $this->_sessionDao->getById($id);
		
		if ($session != null) {
			$expirationTime = (int) $session->modified + $session->lifetime;
			if ($expirationTime > time()) {
				$return = $session->data;
			} else {
				$this->destroy($id);
			}
		}
		return $return;
	}
	
	/**
	 * @see Zend_Session_SaveHandler_Interface::write()
	 */
	public function write($id, $data) 
	{
		$obj			 = new stdClass();
		$obj->session_id = $id;
		$obj->data		 = $data;
		$obj->modified   = time();
		
		$session		 = $this->_sessionDao->getById($id);
		if (null == $session) {
			$obj->lifetime = $this->_lifetime;
			// We could not call:
			//		$this->_sessionDao->insert(new Core_Models_Session(...))
			return $this->_sessionDao->insert($obj);
		} else {
			$obj->lifetime = $session->lifetime;
			return $this->_sessionDao->update($obj);
		}
	}
}
