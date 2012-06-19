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
 * @version		2011-10-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_BackendMenu extends Zend_View_Helper_Abstract
{
	/**
	 * Gets view helper instance
	 * 
	 * @return Core_View_Helper_BackendMenu
	 */
	public function backendMenu()
	{
		return $this;
	}
	
	/**
	 * Gets the items for rendering the Modules menu
	 * 
	 * @return array
	 */
	public function modules()
	{
		Core_Services_Db::connect('master');
		
		$data	 = array();
		$modules = Core_Services_Module::getInstalledModules();
		if (!$modules) {
			return array();
		}
		foreach ($modules as $module) {
			$file = APP_ROOT_DIR . DS . 'modules' . DS . $module->name . DS . 'configs' . DS . 'about.php';
			if (!file_exists($file)) {
				continue;
			}
			$info = include $file;
			if (!$info || !is_array($info) || !isset($info['backendMenu'])) {
				continue;
			}
			$this->view->translator()->setLanguageDir('/modules/' . $module->name . '/languages');
			
			$items = array();
			foreach ($info['backendMenu'] as $route => $itemData) {
				// Only show the menu item that user have right to access
				if ($this->view->accessor()->route($route)) {
					$items[$route] = array(
						'icon'	=> isset($itemData['icon']) ? $itemData['icon'] : null,
						'ajax'  => $itemData['ajax'],
						'title' => $this->view->translator()->_($itemData['translationKey'], $itemData['description']),
					);
				}
			}
			if (count($items) > 0) {
				$data[$module->name] = array(
					'name'	=> $this->view->translator()->_($info['title']['translationKey'], $info['title']['description']),
					'icon'	=> isset($info['icon']) ? $info['icon'] : null,
					'items' => $items,
				);
			}
		}
		
		return $data;
	}
}
