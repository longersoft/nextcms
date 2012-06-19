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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_SkinController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates the skin
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$skin	  = $request->getParam('skin');
		
		$result	  = false;
		$currentTemplate = Core_Services_Config::get('core', 'template', 'default');
		if ($template && $skin && $template == $currentTemplate) {
			$result = true;
			Core_Services_Config::set('core', 'skin', $skin);
		}
		$this->_helper->json(array(
			'result'   => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'template' => $template,
			'skin'	   => $skin,
		));
	}
	
	/**
	 * Edits the content of CSS file
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$skin	  = $request->getParam('skin');
		$file	  = $request->getParam('file');
		$format	  = $request->getParam('format');
		$path	  = DS . 'templates' . DS . $template . DS . 'skins' . DS . $skin . DS . $file;
		$cssFile  = APP_ROOT_DIR . $path;
		
		switch ($format) {
			case 'json':
				// Update the content of CSS file
				$content = $request->getPost('content');
				$result  = @file_put_contents($cssFile, $content);
				$this->_helper->json(array(
					'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'content'  => file_exists($cssFile) ? htmlspecialchars(file_get_contents($cssFile)) : '',
					'path'	   => $path,
					'template' => $template,
					'skin'	   => $skin,
					'file'	   => $file,
				));
				break;
		}
	}
	
	/**
	 * Lists skins of given template
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$template = $request->getParam('template', 'default');
		$format	  = $request->getParam('format');
		
		// The current template
		$currentTemplate = Core_Services_Config::get('core', 'template', 'default');
		
		switch ($format) {
			case 'json':
				// Get array of skins located at the "skins" directory
				$skinsDir	 = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'skins';
				$items		 = array();
				$dirs		 = Core_Base_File::getSubDirectories($skinsDir);
				$currentSkin = Core_Services_Config::get('core', 'skin', 'default');
				
				foreach ($dirs as $skin) {
					// Get the list of CSS files
					$cssFiles	 = array($skin);
					$dirIterator = new DirectoryIterator($skinsDir . DS . $skin);
					foreach ($dirIterator as $f) {
						if ($f->isDot() || $f->isDir()) {
							continue;
						}
						$file = $f->getFilename();
						if (strlen($file) > 4 && strtolower(substr($file, -4)) == '.css') {
							$cssFiles[] = $file;
						}
					}
					
					$items[] = array(
						'template'  => $template,
						'name'		=> $skin,
						'is_active' => ($template == $currentTemplate && $skin == $currentSkin),
						'css_files' => implode(',', $cssFiles),
					);
				}
				
				// Build the data store for the skin grid
				$this->_helper->json(array(
					'identifier' => 'name',
					'items'		 => $items,
				));
				break;
			default:
				$this->view->assign(array(
					'uniqueId'		  => uniqid(),
					'template'		  => $template,
					'currentTemplate' => $currentTemplate,
				));
				break;
		}
	}
}
