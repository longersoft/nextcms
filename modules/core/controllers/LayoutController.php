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

class Core_LayoutController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Edits the layout script
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$layout	  = $request->getParam('layout');
		$format	  = $request->getParam('format');
		$file	  = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'layouts' . DS . $layout;
		
		switch ($format) {
			case 'json':
				// Update the layout script
				$content = $request->getPost('content');
				$result  = @file_put_contents($file, $content);
				$this->_helper->json(array(
					'result' => ($result === false) ? 'APP_RESULT_ERROR' : 'APP_RESULT_OK',
				));
				break;
			default:
				$this->view->assign(array(
					'content'  => file_exists($file) ? htmlspecialchars(file_get_contents($file)) : '',
					'template' => $template,
					'layout'   => $layout,
				));
				break;
		}
	}
	
	/**
	 * Lists layouts of given template
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$request  = $this->getRequest();
		$template = $request->getParam('template', 'default');
		$format	  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				// Get array of layouts located at the "layouts" directory
				$layoutDir	 = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'layouts';
				$items		 = array();
				if (file_exists($layoutDir)) {
					$dirIterator = new DirectoryIterator($layoutDir);
					foreach ($dirIterator as $f) {
						if ($f->isDot() || $f->isDir()) {
							continue;
						}
						$layout  = $f->getFilename();
						$items[] = array(
							'template'		=> $template,
							'name'			=> $layout,
							'last_modified' => date('Y-m-d H:i:s', filemtime($layoutDir . DS . $layout)),
						);
					}
				}
				
				// Build the data store for the layout grid
				$this->_helper->json(array(
					'identifier' => 'name',
					'items'		 => $items,
				));
				break;
				
			default:
				$this->view->assign(array(
					'template' => $template,
					'uniqueId' => uniqid(),
				));
				break;
		}
	}
}
