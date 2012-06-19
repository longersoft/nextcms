<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_ConnectionController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Adds new connection
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$connection = new File_Models_Connection(array(
					'name'		=> $request->getPost('name'),
					'type'		=> $request->getPost('type', 'local'),
					'server'	=> $request->getPost('server'),
					'port'		=> $request->getPost('port'),
					'user_name' => $request->getPost('user_name'),
					'password'  => $request->getPost('password'),
					'init_path' => $request->getPost('init_path'),
				));
				File_Services_Connection::add($connection);
				$this->_helper->json(array(
					'result' => 'APP_RESULT_OK',
				));
				break;
			default:
				break;
		}
	}
	
	/**
	 * Connects
	 * 
	 * @return void
	 */
	public function connectAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getPost('connection_id');
		$connection	  = File_Services_Connection::getById($connectionId);
		$result		  = 'APP_RESULT_ERROR';
		$path		  = '';
		if ($connection) {
			$file = File_Services_File::factory($connection->type, $connection->getProperties());
			if ($file && $file->getConnector()->connect() !== false) {
				$result = 'APP_RESULT_OK';
			}
			$path = $connection->init_path;
			if ($path) {
				$path = './' . ltrim($path, '/');
			}
		}
		
		$this->_helper->json(array(
			'result' => $result,
			'path'	 => $path,
		));
	}
	
	/**
	 * Deletes connection
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getParam('connection_id');
		$format		  = $request->getParam('format');
		$connection	  = File_Services_Connection::getById($connectionId);
		
		switch ($format) {
			case 'json':
				$result = File_Services_Connection::delete($connection);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'connection'	=> $connection,
				));
				break;
		}
	}
	
	/**
	 * Disconnects
	 * 
	 * @return void
	 */
	public function disconnectAction()
	{
		$result = File_Services_Explorer::disconnect();
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Edits the connection
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$connectionId = $request->getParam('connection_id');
		$connection	  = File_Services_Connection::getById($connectionId);
		
		switch ($format) {
			case 'json':
				$result = false;
				if ($connection) {
					$connection->name	   = $request->getPost('name');
					$connection->type	   = $request->getPost('type', 'local');
					$connection->server	   = $request->getPost('server');
					$connection->port	   = $request->getPost('port');
					$connection->user_name = $request->getPost('user_name');
					$connection->init_path = $request->getPost('init_path');
					
					$password = $request->getPost('password');
					if ($password != null && $password != '') {
						$connection->password = ($password != $connection->password) ? $password : null;
					} else {
						$connection->password = '';
					}
					
					$result = File_Services_Connection::update($connection);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'connection'   => $connection,
					'connectionId' => $connectionId,
				));
				break;
		}
	}
	
	/**
	 * Lists connections
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		$this->view->assign('connections', File_Services_Connection::find());
	}
	
	/**
	 * Renames connection
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request	= $this->getRequest();
		$connection = new File_Models_Connection(array(
							'connection_id' => $request->getPost('connection_id'),
							'name'			=> $request->getPost('name'),
						));
		$result		= File_Services_Connection::rename($connection);
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
