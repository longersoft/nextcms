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
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-12-11
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_LanguageController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new language item
	 * 
	 * @return void
	 */
	public function addAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$file    = $request->getParam('file');
		$path	 = $request->getParam('path');
		
		switch ($format) {
			case 'json':
				$key	= $request->getParam('key');
				$text	= $request->getParam('text');
				$result = Core_Services_Language::setLanguageItem($file, ($path == '.') ? $key : ($path . '.' . $key), $text);
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'path'	 => $path,
					'key'	 => $key,
					'text'	 => $text,
				));
				break;
			default:
				$this->view->assign(array(
					'file' => $file,
					'path' => $path,
				));
				break;
		}
	}
	
	/**
	 * Deletes language item
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$file    = $request->getParam('file');
		$path	 = $request->getParam('path');
		
		switch ($format) {
			case 'json':
				$result = Core_Services_Language::deleteLanguageItem($file, $path);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'path'	 => $path,
				));
				break;
			default:
				$fullPath = str_replace('/', DS, $file);
				$fullPath = ltrim($fullPath, DS);
				$fullPath = APP_ROOT_DIR . DS . 'modules' . DS . $fullPath;
				
				$this->view->assign(array(
					'file'	   => $file,
					'path'	   => $path,
					'notFound' => !file_exists($fullPath),
				));
				break;
		}
	}
	
	/**
	 * Edits a language item
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$file    = $request->getParam('file');
		$path	 = $request->getParam('path');
		$text	 = $request->getParam('text');
		
		switch ($format) {
			case 'json':
				$result = Core_Services_Language::setLanguageItem($file, $path, $text);
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'path'	 => $path,
					'text'	 => $text,
				));
				break;
			default:
				$this->view->assign(array(
					'file' => $file,
					'path' => $path,
					'text' => $text,
				));
				break;
		}
	}
	
	/**
	 * Lists language files
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$languages = array();
		$modules   = Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'modules');
		foreach ($modules as $module) {
			$moduleDir = APP_ROOT_DIR . DS . 'modules' . DS . $module;
			$item	   = array(
				'module' => Core_Services_Language::findLanguages($moduleDir . DS . 'languages'),
			);
			foreach (array('hooks', 'plugins', 'tasks', 'widgets') as $type) {
				$item[$type] = Core_Services_Language::findExtensionLanguages($module, $type);
			}
			
			$languages[$module] = $item;
		}
		$this->view->assign('languages', $languages);
	}
}
