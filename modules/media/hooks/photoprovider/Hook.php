<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Media_Hooks_Photoprovider_Hook extends Core_Base_Extension_Hook
{
	public function __construct()
	{
		parent::__construct();
		Core_Base_Hook_Registry::getInstance()->register('Core_Layout_Admin_ShowMenu_media', array($this, 'menu'), true);
		if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
			$this->view->style()->appendStylesheet($this->view->APP_STATIC_URL . '/modules/media/hooks/photoprovider/styles.css');
		}
	}
	
	/**
	 * Shows the menu item in the back-end
	 * 
	 * @return void
	 */
	public function menuAction()
	{
	}
	
	/**
	 * Searches for photos
	 * 
	 * @return void
	 */
	public function searchAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$q		  = $request->getParam('q');
		$default  = array(
			'status'    => null,
			'page'	    => 1,
			'per_page'  => 20,
			'view_size' => 'square',
			'status'    => Media_Models_Photo::STATUS_ACTIVATED,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$photos	  = Media_Services_Photo::find($criteria, $offset, $criteria['per_page']);
		$total	  = Media_Services_Photo::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($photos, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'photos'	=> $photos,
			'paginator' => $paginator,
			'viewSize'  => $criteria['view_size'],
		));
	}
	
	/**
	 * Shows a toolbox attached to the main Toolbox
	 * 
	 * @return void
	 */
	public function showAction()
	{
		$this->view->assign('thumbSizes', array('square', 'thumbnail', 'small', 'crop', 'medium', 'large', 'original'));		
	}
}
