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
 * @version		2012-05-22
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_PermalinkController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Configures permalinks
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		
		$routes			  = $this->getFrontController()->getRouter()->getRoutes();
		$permalinks		  = array();
		$permalinkModules = array();
		foreach ($routes as $name => $route) {
			$defaults = $route->getDefaults();
			if (isset($defaults['permalink']['enabled']) && $defaults['permalink']['enabled']) {
				$module = $defaults['module'];
				$link	= array(
					'route'		  => $name,
					'module'	  => $module,
					'controller'  => $defaults['controller'],
					'action'	  => $defaults['action'],
					'description' => $this->view->translator()->setLanguageDir('/modules/' . $module . '/languages')->_($defaults['permalink']['translationKey']),
					'params'	  => $defaults['permalink']['params'],
					'default'	  => $defaults['permalink']['default'],
				);
				if (isset($defaults['permalink']['predefined'])) {
					$link['predefined'] = $defaults['permalink']['predefined'];
				}
				$permalinks[$name] = $link;
				if (!isset($permalinkModules[$module])) {
					$permalinkModules[$module] = array();
				}
				$permalinkModules[$module][] = $link;
			}
		}
		
		switch ($format) {
			case 'json':
				$routeNames = $request->getPost('routes');
				$types		= $request->getPost('type');
				$urls		= $request->getPost('url');
				
				$output = array();
				foreach ($routeNames as $routeName) {
					if ($types[$routeName] == 'default') {
						continue;
					}
					$url	 = $urls[$routeName][0];
					$route   = $url;
					$reverse = $url;
					$map     = array();
					
					preg_match_all('/{(\w+)}/', $url, $matches);
					if (count($matches) > 0) {
						foreach ($matches[1] as $index => $param) {
							$map[$index + 1] = $param;
							if (isset($permalinks[$routeName]['params'][$param])) {
								$settings = $permalinks[$routeName]['params'][$param];
								$route    = str_replace('{' . $settings['name'] . '}', $settings['regex'], $route);
								$reverse  = str_replace('{' . $settings['name'] . '}', $settings['reverse'], $reverse);
							}
						}
					}
					
					$defaults = $routes[$routeName]->getDefaults();
					$defaults['permalink']['url']  = $url;
					$defaults['permalink']['type'] = $types[$routeName];
					
					$output[$routeName] = array(
						'type'	   => get_class($routes[$routeName]),
						'route'	   => $route,
						'reverse'  => $reverse,
						'map' 	   => $map,
						'defaults' => $defaults,
					);
				}
				
				$result = false;
				$file   = APP_ROOT_DIR . DS . 'configs' . DS . APP_HOST_CONFIG . '_permalink.' . strtolower(APP_ENV) . '.php';
				if (count($output) > 0) {
					$writer = new Core_Base_Config_Writer_Php();
					$writer->setArrayName('routes')
						   ->write($file, new Zend_Config($output));
					$result = true;
				} else {
					// Reset all permalinks to default
					if (file_exists($file)) {
						@unlink($file);
					}
				}
				
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				// Get the current customized links
				$currLinks = array();
				$file	   = APP_ROOT_DIR . DS . 'configs' . DS . APP_HOST_CONFIG . '_permalink.' . strtolower(APP_ENV) . '.php';
				if (file_exists($file)) {
					$config = include_once $file;
					if (is_array($config)) {
						foreach ($config as $routeName => $settings) {
							$currLinks[$routeName] = array(
								'type' => $settings['defaults']['permalink']['type'],
								'url'  => $settings['defaults']['permalink']['url'],
							);
						}
					}
				}
				
				$this->view->assign(array(
					'currLinks'		   => $currLinks,
					'permalinkModules' => $permalinkModules,
				));
				break;
		}
	}
}
