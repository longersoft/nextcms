<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		seo
 * @subpackage	controllers
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Seo_SitemapController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Builds the sitemap
	 * 
	 * @return void
	 */
	public function buildAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$items  = $request->getPost('items');
				$items  = Zend_Json::decode($items);
				$result = Seo_Services_Sitemap::save($items);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('items', Seo_Services_Sitemap::getItems());
				break;
		}
	}
}
