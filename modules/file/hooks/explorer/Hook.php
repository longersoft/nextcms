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
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Hooks_Explorer_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_file', array($this, 'menu'), true);
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/modules/file/hooks/explorer/styles.css');
		}
	}
	
	/**
	 * Adds a menu item to the back-end menu to show the Explorer toolbox
	 *
	 * @return void
	 */
	public function menuAction()
	{
	}
	
	/**
	 * Shows the toolbox
	 * 
	 * @return void
	 */
	public function showAction()
	{
	}
	
	/**
	 * Searchs for files
	 * 
	 * @return void
	 */
	public function searchAction()
	{
		$rootDir = APP_ROOT_DIR . DS . 'upload';
		
		$request = $this->getRequest();
		$q		 = $request->getParam('q');
		$default = array(
			'path'	    => '',
			'page'	    => 1,
			'per_page'  => 30,
			'view_type' => 'grid',
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$criteria['path'] = ltrim($criteria['path'], DS);
		$criteria['path'] = rtrim($criteria['path'], DS);
		
		// FIXME: Remove . and .. from the begining of the path
		
		$fullPath	 = $rootDir . DS . $criteria['path'];
		$dirIterator = new DirectoryIterator($fullPath);
		
		$files = array();
		foreach ($dirIterator as $f) {
			if ($f->isDot()) {
				continue;
			}
			$name     = $fullPath . DS . $f->getFilename();
			$pathInfo = pathinfo($name);
			$files[]  = array(
				'name'	    => $f->getFilename(),
				'path'		=> str_replace(DS, '/', $criteria['path'] . DS . $f->getFilename()),
				'is_dir'    => $f->isDir(),
				'modified'  => @filemtime($name),
				'size'	    => $f->isDir() ? 0 : @filesize($name),
				'extension' => $f->isDir() ? null : strtolower($pathInfo['extension']),
			);
		}

		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($files));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'files'		=> array_slice($files, $offset, $criteria['per_page']),
			'criteria'  => $criteria,
			'paginator' => $paginator,
			'uploadDir' => 'upload',
		));
	}
}
