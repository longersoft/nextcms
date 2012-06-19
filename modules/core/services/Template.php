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
 * @subpackage	services
 * @since		1.0
 * @version		2012-02-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_Template
{
	/**
	 * @var string
	 */
	const KEY_CURR_TEMPLATE = 'Core_Services_Template_CurrTemplate';
	
	/**
	 * Gets current template
	 * 
	 * @return string
	 */
	public static function getCurrentTemplate()
	{
		if (!Zend_Registry::isRegistered(self::KEY_CURR_TEMPLATE) 
			|| Zend_Registry::get(self::KEY_CURR_TEMPLATE) == null 
			|| Zend_Registry::get(self::KEY_CURR_TEMPLATE) == '')
		{
			Zend_Registry::set(self::KEY_CURR_TEMPLATE, Core_Services_Config::get('core', 'template', 'default'));
		}
		
		return Zend_Registry::get(self::KEY_CURR_TEMPLATE);
	}
	
	/**
	 * Installs a template. It adds all pages found in the template configuration file
	 * 
	 * @param string $template The template name
	 * @return bool
	 */
	public static function install($template)
	{
		if (!$template || $template == 'admin') {
			return false;
		}
		
		// Open the template configuration file
		$file = APP_ROOT_DIR . DS . 'templates' . DS . $template . DS . 'about.php';
		if (!file_exists($file)) {
			return false;
		}
		$config = include $file;
		if (!is_array($config) || !isset($config['pages'])) {
			return false;
		}
		
		// Remove all pages belong to the template
		Core_Services_Page::deleteByTemplate($template);
		
		// Get the front-end routes
		$language		= Core_Services_Config::get('core', 'language_code', 'en_US');
		$frontendRoutes = Core_Services_Page::getFrontendRoutes($language);
		
		// Add pages
		$conn		= Core_Services_Db::getConnection();
		$daoService = Core_Services_Dao::factory(array(
											'module' => 'core',
											'name'   => 'Page',
									   ))
									   ->setDbConnection($conn);
		
		foreach ($config['pages'] as $routeName) {
			if (isset($frontendRoutes[$routeName])) {
				$page = new Core_Models_Page(array(
					'name'	   => $frontendRoutes[$routeName]['description'],
					'route'	   => $routeName,
					'url'	   => '',
					'template' => $template,
					'layout'   => null,
					'language' => $language,
				));
				
				// Add pages
				$pageId = $daoService->add($page);
				$page->page_id = $pageId;
				
				// Parse the layout from XML file
				$layout = Core_Services_PageLayout::getConfigFile($page);
				if ($layout) {
					$layout		  = Core_Services_PageLayout::loadXmlLayout($layout);
					$page->layout = Zend_Json::encode($layout);
					
					Core_Services_Page::updateLayout($page);
				}
			}
		}
		
		// Generate layout mapping
		Core_Services_Page::generateMappingFile($template);
		
		return true;
	}
	
	/**
	 * Uninstalls a template. It removes all pages of the template
	 * 
	 * @param string $template The template name
	 * @return bool
	 */
	public static function uninstall($template)
	{
		if (!$template || $template == 'admin') {
			return false;
		}
		
		// Remove all pages
		Core_Services_Page::deleteByTemplate($template);
		
		// Generate layout mapping
		Core_Services_Page::generateMappingFile($template);
		
		return true;
	}
}
