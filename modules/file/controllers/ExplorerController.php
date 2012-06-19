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

class File_ExplorerController extends Zend_Controller_Action
{
	/**
	 * Map the file's extension with the programming languages supported by Dojo highlight package
	 * 
	 * @var array
	 */
	private static $_HIGHLIGHT_LANGUAGES = array(
		'cpp'   => 'cpp',
		'css'   => 'css',
		'java'  => 'java',
		'js'    => 'javascript',
		'html'  => 'html',
		'pas'   => 'delphi',
		'py'	=> 'python',
		'sql'   => 'sql',
		'xml'   => 'xml',
	
		// Not supported by Dojo
		'php'   => 'html',
		'phtml' => 'html',
	);
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new directory
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$connectionId = $request->getParam('connection_id');
		
		switch ($format) {
			case 'json':
				$path		= $request->getParam('path');
				$name		= $request->getParam('name');
				
				$connection = File_Services_Connection::getById($connectionId);
				$file		= File_Services_File::factory($connection->type, $connection->getProperties());
				
				$result = $file->createSubDir($path, $name);
				$this->_helper->json(array(
					'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'path'			=> $request->getParam('path'),
					'connection_id' => $connectionId,
				));
				break;
		}
	}
	
	/**
	 * Compress file
	 * 
	 * @return void
	 */
	public function compressAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$path		  = $request->getParam('path');
		$connectionId = $request->getParam('connection_id');
		$pathInfo	  = pathinfo($path);
		
		switch ($format) {
			case 'json':
				$adapter	 = $request->getPost('adapter');
				$destination = $request->getPost('destination');
				$overwrite	 = $request->getPost('overwrite');
				$overwrite	 = ($overwrite == null) ? false : true;
				
				$connection	 = File_Services_Connection::getById($connectionId);
				$result		 = File_Services_Explorer::compress($adapter, $connection, array($path), $destination, $overwrite);
				$this->_helper->json(array(
										'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
										'path'	 => $pathInfo['dirname'],
									));
				break;
				
			default:
				$compressableExts = $this->view->archive()->getCompressableExts();
				$compressableExts = ($compressableExts == '') ? array() : explode(',', $compressableExts);
				
				$destination	  = null;
				if (count($compressableExts) > 0) {
					$destination = $pathInfo['dirname'] . '/' . $pathInfo['basename'] . '.' . $compressableExts[0];
				}
				
				$this->view->assign(array(
					'compressableExts' => $compressableExts,
					'connection_id'	   => $connectionId,
					'path'			   => $path,
					'destination'	   => $destination,
				));
				break;
		}
	}
	
	/**
	 * Copies file
	 * 
	 * @return void
	 */
	public function copyAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getParam('connection_id');
		$sourcePaths  = $request->getParam('source_paths');
		$targetPath	  = $request->getParam('target_path');
		$overwrite	  = $request->getParam('overwrite', 'false') == 'true';
		
		$connection = File_Services_Connection::getById($connectionId);
		$file		= File_Services_File::factory($connection->type, $connection->getProperties());
		
		$result		= true;
		if (is_array($sourcePaths)) {
			foreach ($sourcePaths as $source) {
				$result = $result && $file->copyFile($source, $targetPath, $overwrite);
			}
		}
		$this->_helper->json(array(
			'result' => ($result == false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Deletes file
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$connectionId = $request->getParam('connection_id');
		$directory	  = $request->getParam('directory', 'true') == 'true';
		$path		  = $request->getParam('path');
				
		switch ($format) {
			case 'json':
				$connection = File_Services_Connection::getById($connectionId);
				$file		= File_Services_File::factory($connection->type, $connection->getProperties());
				
				$result = $directory ? $file->deleteDirRescursive($path) : $file->deleteFile($path);
				$path	= pathinfo($path);
				$this->_helper->json(array(
					'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
					'path'	 => $path['dirname'],
				));
				break;
			default:
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'item'			=> array(
											'path'		=> $path,
											'directory' => $directory,
										),
				));
				break;
		}
	}
	
	/**
	 * Downloads file
	 * 
	 * @return void
	 */
	public function downloadAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getParam('connection_id');
		$path		  = $request->getParam('path');
		
		$connection = File_Services_Connection::getById($connectionId);
		$file		= File_Services_File::factory($connection->type, $connection->getProperties());
		
		$tempFile	= $file->downloadFile($path);
		if (file_exists($tempFile)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($tempFile));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($tempFile));
			ob_clean();
			flush();
			readfile($tempFile);
		}
		exit();
	}
	
	/**
	 * Edits file
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getParam('connection_id');
		$format		  = $request->getParam('format');
		$path		  = $request->getParam('path');
		$pathInfo	  = pathinfo($path);
		
		$connection = File_Services_Connection::getById($connectionId);
		$file		= File_Services_File::factory($connection->type, $connection->getProperties());
		
		switch ($format) {
			case 'json':
				$content = $request->getPost('content');
				
				// Save the content to the temp file
				$tempDir  = 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
				$tempDir  = APP_TEMP_DIR . DS . $tempDir;
				$tempFile = $tempDir . DS . $pathInfo['basename'];
				
				$result = true;
				if (file_put_contents($tempFile, $content) === false) {
					$result = false;
				} else {
					// Upload the temp file to the server
					// FIXME: Do I have to set the file's permissions?
					$result = $file->uploadFile($tempFile, $pathInfo['dirname'], true);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
				
			default:
				// Download the file to local
				$downloadedFile = $file->downloadFile($path);
				
				// Prepare the temp folders
				$tempDir = 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
				Core_Base_File::createDirectories($tempDir, APP_TEMP_DIR);
				$tempDir = APP_TEMP_DIR . DS . $tempDir;
				@chmod($tempDir, 0777);
				
				// Copy the downloaded file to the temp foler
				$tempFile = $tempDir . DS . $pathInfo['basename'] . '_read';
				@copy($downloadedFile, $tempFile);
				
				// Get file's content
				$content = (htmlspecialchars(file_get_contents($tempFile)));
				
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'path'			=> $path,
					'content'		=> $content,
				));
				break;
		}
	}
	
	/**
	 * Extracts compressed file
	 * 
	 * @return void
	 */
	public function extractAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$path		  = $request->getParam('path');
		$connectionId = $request->getParam('connection_id');
		$pathInfo	  = pathinfo($path);
		
		switch ($format) {
			case 'json':
				$destination = $request->getPost('destination');
				$overwrite	 = $request->getPost('overwrite');
				$overwrite	 = ($overwrite == null) ? false : true;
				
				$connection	 = File_Services_Connection::getById($connectionId);
				$result		 = File_Services_Explorer::extract($connection, $path, $destination, $overwrite);
				$this->_helper->json(array(
										'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
										'path'	 => $pathInfo['dirname'],
									));
				break;
				
			default:
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'path'			=> $path,
					'destination'	=> $pathInfo['dirname'],
				));
				break;
		}
	}	
	
	/**
	 * Lists files
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		// When disconnecting, I want to empty the list
		if ($request->getParam('empty', 'false') == 'true') {
			$this->_helper->json(array(
				'total' => 0,
				'items' => array(),
			));
		}
		
		switch ($format) {
			case 'json':
				$connectionId = $request->getParam('connection_id');
				$path		  = $request->getParam('path');
				$dirsOnly	  = $request->getParam('dirs_only');
				$dirsOnly	  = ($dirsOnly == 'true') ? true : ($dirsOnly == 'false' ? false : null);
				$target		  = $request->getParam('target');
				
				$connection = File_Services_Connection::getById($connectionId);
				$file		= File_Services_File::factory($connection->type, $connection->getProperties());
				
				if (!is_string($path) || $target == 'datagrid') {
					$criteria = array(
						'dirs_only'			 => $dirsOnly,
						'name'				 => $request->getParam('name', null),
						'hidden_files'		 => $request->getParam('hidden_files', 'false') == 'true',
						'case_sensitive'	 => $request->getParam('case_sensitive', 'false') == 'true',
						'regular_expression' => $request->getParam('regular_expression', 'false') == 'true',
						'recurse'			 => $request->getParam('recurse', 'false') == 'true',
					);
					
					// List children directories/files
					$files  = $file->getFiles($path, $criteria);
					$total  = count($files);
					
					// Sort the files if the sorting is requested
					if ($sortSpec = $request->getParam('sort')) {
						$sortSpec = Zend_Json::decode($sortSpec);
						
						$comparatorChain = new File_Services_Comparator_Chain();
						foreach ($sortSpec as $sortItem) {
							// The attribute and descending properties are passed by Dojo DataGrid
							$comparatorChain->addComparator(new File_Services_Comparator_Field($sortItem['attribute'], $sortItem['descending']));
						}
						usort($files, array($comparatorChain, "compare"));
					}
					
					// Paginator
					// start and count are passed by Dojo DataGrid automatically
					// when I set value for the rowsPerPage property of the grid
					$start  = $request->getParam('start', 0);
					$count  = $request->getParam('count', $total);
					$files  = array_slice($files, $start, $count);
					
					$result = array(
						'total' => $total,
						'items' => $files,
					);
					$this->_helper->json($result);
				} else {
					// Load the info of given directory/file.
					// It is required by dijit.Tree widget when I want to load the data for each node in the directory tree
					$pathInfo = pathinfo($path);
					$fileInfo = $file->getFileInfo($pathInfo['basename'], $pathInfo['dirname'], $dirsOnly);
					$this->_helper->json($fileInfo);
				}
				break;
				
			default:
				break;
		}
	}
	
	/**
	 * Moves file
	 * 
	 * @return void
	 */
	public function moveAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$connectionId = $request->getParam('connection_id');
		$sourcePaths  = $request->getParam('source_paths');
		$targetPath	  = $request->getParam('target_path');
		$overwrite	  = $request->getParam('overwrite', 'false') == 'true';
		
		$connection = File_Services_Connection::getById($connectionId);
		$file		= File_Services_File::factory($connection->type, $connection->getProperties());
		
		$result		= true;
		if (is_array($sourcePaths)) {
			foreach ($sourcePaths as $source) {
				$result = $result && $file->moveFile($source, $targetPath, $overwrite);
			}
		}
		$this->_helper->json(array(
			'result' => ($result == false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
		));
	}
	
	/**
	 * Sets file permissions
	 * 
	 * @return void
	 */
	public function permAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$connectionId = $request->getParam('connection_id');
		$path		  = $request->getParam('path');
				
		switch ($format) {
			case 'json':
				$recurse = $request->getPost('recurse');
				$recurse = ($recurse === null) ? false : true;
				
				$binary  = '';
				foreach (array('owner', 'group', 'other') as $user) {
					foreach (array('read', 'write', 'execute') as $permission) {
						if ($request->getPost($user . '_' . $permission) === null) {
							$binary .= '0';
						} else {
							$binary .= '1';
						}
					}
				}
				$permissionNumeric = bindec($binary);
				
				$connection = File_Services_Connection::getById($connectionId);
				$file		= File_Services_File::factory($connection->type, $connection->getProperties());
				$result		= $file->setPermission($path, $permissionNumeric, $recurse);
				$this->_helper->json(array(
					'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
				));
				break;
			default:
				$perms = $request->getParam('perms');
				
				// Convert the permissions from numeric to string format
				$permsString = "";
				if (strlen($perms) < 3) {
					$permsString = str_repeat('-', 9);
				} else {
					for ($i = 0; $i < strlen($perms); $i++) {
						$p = (int) $perms[$i]; 
						$permsString .= ($p & 04) ? "r" : "-";		// Read permission
						$permsString .= ($p & 02) ? "w" : "-";		// Write
						$permsString .= ($p & 01) ? "x" : "-";		// Execute
					}
				}
				
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'path'			=> $path,
					'permsString'	=> $permsString,
				));
				break;
		}
	}
	
	/**
	 * Renames file
	 * 
	 * @return void
	 */
	public function renameAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$connectionId = $request->getParam('connection_id');
				
		switch ($format) {
			case 'json':
				$path		 = $request->getParam('path');
				$currentName = $request->getParam('currentName');
				$newName	 = $request->getParam('name');
				
				$connection = File_Services_Connection::getById($connectionId);
				$file		= File_Services_File::factory($connection->type, $connection->getProperties());
				
				$result = $file->renameFile($currentName, $newName, $path);
				$this->_helper->json(array(
					'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
				));
				break;
			default:
				$path	  = $request->getParam('path');
				$pathInfo = pathinfo($path);
				
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'item'			=> array(
										'path'		=> $pathInfo['dirname'],
										'name'		=> $pathInfo['basename'],
										'directory' => $request->getParam('directory', 'true') == 'true',
									),
				));
				break;
		}
	}
	
	/**
	 * Uploads file
	 * 
	 * @return void
	 */
	public function uploadAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$path		  = $request->getParam('path');
		$connectionId = $request->getParam('connection_id');
		
		switch ($format) {
			case 'json':
				$uploadedFiles = $_FILES['uploadedfiles'];
				$overwrite	   = $request->getPost('overwrite');
				$overwrite	   = ($overwrite == null) ? false : true;
				
				// Prepare the temp folders
				$tempDir   = 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
				Core_Base_File::createDirectories($tempDir, APP_TEMP_DIR);
				$tempDir   = APP_TEMP_DIR . DS . $tempDir;
				@chmod($tempDir, 0777);
				
				// Move the uploaded files to the temp folders
				$numFiles  = count($uploadedFiles['name']);
				$tempFiles = array();
				for ($i = 0; $i < $numFiles; $i++) {
					$tempFiles[$i] = $tempDir . DS . $uploadedFiles['name'][$i];
					move_uploaded_file($uploadedFiles['tmp_name'][$i], $tempFiles[$i]);		
				}
				
				// Upload the temp files to the server
				$connection = File_Services_Connection::getById($connectionId);
				$file		= File_Services_File::factory($connection->type, $connection->getProperties());
				
				$result = array();
				for ($i = 0; $i < count($tempFiles); $i++) {
					$file->uploadFile($tempFiles[$i], $path, $overwrite);
					$result[] = array(
						'path' => $path,
					);
					
					// Remove the temp file
					unlink($tempFiles[$i]);
				}
				
				// Returns the array in JSON format 
				// that will be processed by handler of onComplete() event of Dojo Uploader widget
				$this->_helper->json($result);
				break;
			default:
				$this->view->assign(array(
					'connection_id' => $connectionId,
					'path'			=> $path,
				));
				break;
		}
	}
	
	/**
	 * Views file
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$path		  = $request->getParam('path');
		$connectionId = $request->getParam('connection_id');
		
		$connection = File_Services_Connection::getById($connectionId);
		$file		= File_Services_File::factory($connection->type, $connection->getProperties());
		
		// Download the file to local
		$pathInfo		= pathinfo($path);
		$downloadedFile = $file->downloadFile($path);
		
		// Prepare the temp folders
		$tempDir	 = 'file_explorer_' . Zend_Auth::getInstance()->getIdentity()->user_id;
		Core_Base_File::createDirectories($tempDir, APP_TEMP_DIR);
		$tempDirPath = APP_TEMP_DIR . DS . $tempDir;
		@chmod($tempDirPath, 0777);
		
		// Copy the downloaded file to the temp foler
		$tempFile = $tempDirPath . DS . $pathInfo['basename'];
		@copy($downloadedFile, $tempFile);
		
		// If the file is image, returns the URL to view
		$extension = strtolower($pathInfo['extension']);
		if (in_array($extension, array('bmp', 'gif', 'jpg', 'jpeg', 'png'))) {
			$url = basename(APP_TEMP_DIR) . '/' . $tempDir . '/' . $pathInfo['basename'];
			$this->_helper->json(array(
									'url'   => $this->view->APP_ROOT_URL . '/' . $url, 
									'title' => $pathInfo['basename'],
								));
		} else {
			// Get file's content
			$content	= (htmlspecialchars(file_get_contents($tempFile)));
			
			$pathInfo	= pathinfo($tempFile);
			$extension  = $pathInfo['extension'];
			$highlight  = isset(self::$_HIGHLIGHT_LANGUAGES[$extension]) ? self::$_HIGHLIGHT_LANGUAGES[$extension] : '_www';
	
			$this->view->assign(array(
				'path'		=> $path,
				'content'	=> $content,
				'highlight' => $highlight,
			));
		}
	}
}
