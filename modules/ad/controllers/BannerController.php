<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-10-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Ad_BannerController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Activates banner
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$bannerId = $request->getPost('banner_id');
		$banner   = Ad_Services_Banner::getById($bannerId);
		if (!$banner) {
			$this->_helper->json(array(
				'result' => 'APP_RESULT_ERROR',
			));
		} else {
			$banner->status = $banner->status == Ad_Models_Banner::STATUS_ACTIVATED
								? Ad_Models_Banner::STATUS_NOT_ACTIVATED
								: Ad_Models_Banner::STATUS_ACTIVATED;
			$result = Ad_Services_Banner::updateStatus($banner);
			$this->_helper->json(array(
				'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			));
		}
	}
	
	/**
	 * Adds new banner
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		switch ($format) {
			case 'json':
				$banner = new Ad_Models_Banner(array(
					'title'		   => $request->getPost('title'),
					'format'	   => $request->getPost('banner_format'),
					'code'		   => $request->getPost('code'),
					'target'	   => $request->getPost('target', '_self'),
					'target_url'   => $request->getPost('target_url'),
					'url'		   => $request->getPost('url'),
					'created_date' => date('Y-m-d H:i:s'),
					'from_date'	   => $request->getPost('from_date'),
					'to_date'	   => $request->getPost('to_date'),
				));
				
				if ($banner->from_date == '') {
					$banner->from_date = null;
				}
				if ($banner->to_date == '') {
					$banner->to_date = null;
				}
				
				$links = $request->getPost('links');
				$zones = $request->getPost('zones');
				if ($links && $zones) {
					$pages = array();
					foreach ($links as $index => $link) {
						$pages[] = array(
							'zone_id' => $zones[$index],
							'route'   => null,
							'url'	  => $link,
						);
					}
					$banner->pages = $pages;
				}
				
				$result = Ad_Services_Banner::add($banner);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('zones', Ad_Services_Zone::find());
				break;
		}
	}
	
	/**
	 * Deletes banner
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$format	  = $request->getParam('format');
		$bannerId = $request->getParam('banner_id');
		
		$banner   = Ad_Services_Banner::getById($bannerId);
		switch ($format) {
			case 'json':
				$result = Ad_Services_Banner::delete($banner);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('banner', $banner);
				break;
		}
	}
	
	/**
	 * Updates banner
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$format	  = $request->getParam('format');
		$bannerId = $request->getParam('banner_id');
		$banner   = Ad_Services_Banner::getById($bannerId);
		
		switch ($format) {
			case 'json':
				if (!$banner) {
					$this->_helper->json(array(
						'result' => 'APP_RESULT_ERROR',
					));
					exit();
				}
				
				$banner->title		= $request->getPost('title');
				$banner->format		= $request->getPost('banner_format');
				$banner->code		= $request->getPost('code');
				$banner->target		= $request->getPost('target', '_self');
				$banner->target_url = $request->getPost('target_url');
				$banner->url		= $request->getPost('url');
				$banner->from_date 	= $request->getPost('from_date');
				$banner->to_date	= $request->getPost('to_date');
				
				if ($banner->from_date == '') {
					$banner->from_date = null;
				}
				if ($banner->to_date == '') {
					$banner->to_date = null;
				}
				
				$links = $request->getPost('links');
				$zones = $request->getPost('zones');
				if ($links && $zones) {
					$pages = array();
					foreach ($links as $index => $link) {
						$pages[] = array(
							'zone_id' => $zones[$index],
							'route'   => null,
							'url'	  => $link,
						);
					}
					$banner->pages = $pages;
				}
				
				$result = Ad_Services_Banner::update($banner);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'banner' => $banner,
					'zones'  => Ad_Services_Zone::find(),
					'pages'  => Ad_Services_Banner::getStaticLinks($banner),
				));
				break;
		}
	}
	
	/**
	 * Lists banners
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		$q		 = $request->getParam('q');
		$default = array(
			'page'	   => 1,
			'per_page' => 20,
			'status'   => null,
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$banners  = Ad_Services_Banner::find($criteria, $offset, $criteria['per_page']);
		$total	  = Ad_Services_Banner::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($banners, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
				  
		$this->view->assign(array(
			'banners'	=> $banners,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Places banner on pages
	 * 
	 * @return void
	 */
	public function placeAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$pageId  = $request->getPost('page_id');
				$banners = $request->getPost('banners');
				$result  = false;
				if ($pageId) {
					$page    = Core_Services_Page::getById($pageId);
					$banners = $banners == "[]" ? array() : Zend_Json::decode($banners);
					$result  = Ad_Services_Banner::addAssociationPage($page, $banners);
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$templates = array();
				$pages	   = Core_Services_Page::find();
				foreach ($pages as $page) {
					if (!isset($templates[$page->template])) {
						$templates[$page->template] = array();
					}
					
					// Get the banners on page
					$banners = Ad_Services_Banner::getAssociationBanners($page);
					$items   = array();
					foreach ($banners as $banner) {
						$items[] = $banner->getProperties();
					}
					
					$templates[$page->template][$page->page_id . ''] = array(
						'page_id'  => $page->page_id,
						'template' => $page->template,
						'name'	   => $page->name,
						'layout'   => $page->layout,
						'banners'  => $items,
					);
				}
				$this->view->assign('templates', $templates);
				break;
		}
	}
}
