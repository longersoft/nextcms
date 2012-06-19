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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Filters_WidgetParser
{
	/**
	 * The widget pattern
	 * See the LayoutContainer::_getHtmlData() method
	 * (/modules/core/js/views/LayoutContainer.js)
	 * 
	 * @var string
	 */
	const WIDGET_PATTERN = "/\[app:widget(\s+)data='(.+)'\]\[\/app:widget\]/";
	
	/**
	 * @var Zend_View
	 */
	private static $_view;
	
	/**
	 * @var array
	 */
	private static $_requestParams = null;
	
	/**
	 * Parses the widget tags in given content and renders the widgets
	 * 
	 * @param string $content The content
	 * @return string
	 */
	public static function parse($content)
	{
		return preg_replace_callback(self::WIDGET_PATTERN, 'Core_Filters_WidgetParser::render', $content);
	}
	
	/**
	 * Renders widget
	 * 
	 * @param array $matches
	 * @return string
	 */
	public static function render($matches)
	{
		// Get the widget's module and name
		$data   = $matches[2];
		$array  = Zend_Json::decode($data);
		$module = $array['module'];
		$name	= $array['name'];
		$params = $array['params'];
		
		// Define the request's params
		if (self::$_requestParams == null) {
			self::$_requestParams = array();
			$route = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRoute();
			if ($route instanceof Zend_Controller_Router_Route_Regex) {
				$map = $route->getVariables();
				foreach ($map as $k) {
					self::$_requestParams[$k] = Zend_Controller_Front::getInstance()->getRequest()->getParam($k);
				}
			}
		}
		
		// Define the widget's params
		$widgetParams = array();
		if (is_array($params)) {
			foreach ($params as $k => $v) {
				if ($v == '__AUTO__' && isset(self::$_requestParams[$k])) {
					$widgetParams[$k] = self::$_requestParams[$k];
				} else {
					$widgetParams[$k] = $v;
				}
			}
		}
		
		// Render the widget
		if (self::$_view == null) {
			self::$_view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		}
		$output = self::$_view->widget($name, $module, array('params' => $widgetParams));
		$output = '<div class="appEmbedWidget">' . $output . '</div>';
		return $output;
	}
	
	/**
	 * Removes all widgets found in given content
	 * 
	 * @param string $content The content
	 * @return string
	 */
	public static function removeWidgets($content)
	{
		return preg_replace_callback(self::WIDGET_PATTERN, 'Core_Filters_WidgetParser::remove', $content);
	}
	
	/**
	 * Removes widgets in each match
	 * 
	 * @param array $matches
	 * @return string
	 */
	public static function remove($matches)
	{
		return '';
	}
}
