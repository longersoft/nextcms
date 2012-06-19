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
 * @subpackage	views
 * @since		1.0
 * @version		2012-03-03
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_PageMapper
{
	/**
	 * Maps the current route with associated page
	 * 
	 * @return string The path to page's layout file
	 */
	public function pageMapper()
	{
		// Get current page that is stored by the Core_Controllers_Plugins_PageMapper plugin
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$page  	 = $request->getParam('appCurrentPage');
		
		if (!$page) {
			return null;
		}
		
		// Include the layout file
		$layoutFile = Core_Services_PageLayout::buildPhpLayoutPath($page);

//		return file_exists($layoutFile) ? $layoutFile : null;
		if (!file_exists($layoutFile)) {
			// Try to import the layout from XML file
			$xml = Core_Services_PageLayout::getConfigFile($page);
			if (file_exists($xml)) {
				$layout = Core_Services_PageLayout::loadXmlLayout($xml);
				$layout = Core_Services_PageLayout::buildPhpLayout($layout);
				@file_put_contents($layoutFile, $layout);
			} else {
				return null;
			}
		}
		return $layoutFile;
	}
}
