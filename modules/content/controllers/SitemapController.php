<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_SitemapController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Views the sitemap set
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		$this->_helper->getHelper('layout')->disableLayout();
		
		$file = Content_Services_Sitemap::getFile();
		if (!file_exists($file)) {
			$container = new Zend_Navigation();
			$xml = $this->view->navigation($container)
							  ->sitemapIndex()
							  ->setFormatOutput(true)
							  ->render();
			@file_put_contents($file, $xml);
		}
		
		$xml = file_get_contents($file);
		$this->getResponse()
			 ->setHeader('Content-Type', 'application/xml; charset=utf-8')
			 ->setBody($xml);
	}
	
	/**
	 * Views the articles sitemap
	 * 
	 * @return void
	 */
	public function viewAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		$year    = $request->getParam('year', date('Y'));
		$month   = $request->getParam('month', date('m'));
		$file	 = Content_Services_Sitemap::getArchiveFile($year, $month);
		if (!file_exists($file)) {
			$container = new Zend_Navigation();
			$xml = $this->view->navigation($container)
							  ->sitemap()
							  ->setFormatOutput(true)
							  ->render();
			@file_put_contents($file, $xml);
		}
		
		$xml = file_get_contents($file);
		$this->getResponse()
			 ->setHeader('Content-Type', 'application/xml; charset=utf-8')
			 ->setBody($xml);
	}
}
