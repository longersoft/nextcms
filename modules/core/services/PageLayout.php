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
 * @version		2012-04-04
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Services_PageLayout
{
	/**
	 * Builds the path of PHP file that defines the layout of page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return string
	 */
	public static function buildPhpLayoutPath($page)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		
		$layoutDir = APP_ROOT_DIR . DS . 'templates' . DS . $page->template . DS . 'pages';
		return $page->url
				? $layoutDir . DS . $page->route . '.' . md5($page->url) . '.' . $page->language . '.php'
				: $layoutDir . DS . $page->route . '.' . $page->language . '.php';
	}
	
	/**
	 * Builds the path of XML file that defines the layout of page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return string
	 */
	public static function buildXmlLayoutPath($page)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		
		$layoutDir = APP_ROOT_DIR . DS . 'templates' . DS . $page->template . DS . 'pages';
		return $page->url
				? $layoutDir . DS . $page->route . '.' . md5($page->url) . '.' . $page->language . '.xml'
				: $layoutDir . DS . $page->route . '.' . $page->language . '.xml';
	}
	
	/**
	 * Gets the XML file that defines the layout of page
	 * 
	 * @param Core_Models_Page $page The page instance
	 * @return string
	 */
	public static function getConfigFile($page)
	{
		if (!$page || !($page instanceof Core_Models_Page)) {
			throw new Exception('The param is not an instance of Core_Models_Page');
		}
		
		$layoutDir		 = APP_ROOT_DIR . DS . 'templates' . DS . $page->template . DS . 'pages';
		$localizedLayout = $page->url
						   ? $layoutDir . DS . $page->route . '.' . md5($page->url) . '.' . $page->language . '.xml'
						   : $layoutDir . DS . $page->route . '.' . $page->language . '.xml';
		$defaultLayout   = $layoutDir . DS . $page->route . '.xml';
		if (file_exists($localizedLayout)) {
			return $localizedLayout;
		} elseif (file_exists($defaultLayout)) {
			return $defaultLayout;
		}
		return null;
	}
	
	/**
	 * Loads layout from a XML file
	 * 
	 * @param string $layoutFile The XML file that defines the layout of page
	 * @return array
	 */
	public static function loadXmlLayout($layoutFile)
	{
		// Parse the layout from XML file
		$xml = simplexml_load_file($layoutFile);
		return self::_parseNode($xml->borderContainer, $xml);
	}
	
	/**
	 * Parses a XML node that represents a layout element
	 * 
	 * @param SimpleXMLElement $element The node element
	 * @param SimpleXMLElement $parent The parent node
	 * @param int $numChildren Number of children of the parent node
	 * @param int $index The index of node
	 * @return array
	 */
	private static function _parseNode($element, $parent, $numChildren = 1, $index = 0)
	{
		$attrs		 = $element->attributes();
		$parentAttrs = $parent->attributes();
		$layout		 = array(
			'properties' => array(
				'id'		=> (string) $attrs->id,
				'css_class' => (string) $attrs->cssClass,
				'css_style' => (string) $attrs->cssStyle,
			),
		);
		if ($attrs->title) {
			$layout['properties']['title'] = (string) $attrs->title;
		}
		if ($attrs->cacheLifetime && ($lifetime = (int) $attrs->cacheLifetime) > 0) {
			$layout['properties']['cache_lifetime'] = $lifetime;
		}
		
		$name = $element->getName();
		switch ($name) {
			case 'borderContainer':
				$layout['cls']	  = 'dijit.layout.BorderContainer';
				$layout['region'] = (string) $attrs->region;
				break;
			
			case 'gridContainer':
				$layout['cls']	    = 'dojox.layout.GridContainer';
				$layout['numZones'] = count($element->zone);
				// Set the numZones attribute
				$element->addAttribute('numZones', $layout['numZones']);
				break;
			
			case 'zone':
				$layout['cls']		 = 'gridContainerZone';
				$layout['numZones']  = (string) $parentAttrs->numZones;
				$layout['zoneIndex'] = $index;
				// Set the numZones and zoneIndex attributes
				$element->addAttribute('numZones', $layout['numZones']);
				$element->addAttribute('zoneIndex', $index);
				break;
				
			case 'tabContainer':
				$layout['cls'] = 'dijit.layout.TabContainer';
				if ($parent->getName() == 'zone') {
					// The numZones and zoneIndex attributes are set above
					$layout['numZones']		= (string) $parentAttrs->numZones;
					$layout['zoneIndex']	= (string) $parentAttrs->zoneIndex;
					$layout['numPortlets']  = $numChildren;
					$layout['portletIndex'] = $index;
				}
				break;
			
			case 'widget':
				$layout['cls']				= 'core.js.views.LayoutPortlet';
				// The numZones and zoneIndex attributes are set above
				$layout['numZones']			= (string) $parentAttrs->numZones;
				$layout['zoneIndex']		= (string) $parentAttrs->zoneIndex;
				$layout['numPortlets']		= $numChildren;
				$layout['portletIndex']		= $index;
				$layout['widget']['module'] = (string) $attrs->module;
				$layout['widget']['name']	= (string) $attrs->name;
				$layout['widget']['title']  = (string) $attrs->module . '/' . (string) $attrs->name;
				$layout['widget']['params'] = array();
				
				// Get widget params
				if ($attrs->title) {
					$layout['widget']['params']['title'] = (string) $attrs->title;
				}
				if ($element->params) {
					foreach ($element->params->param as $paramElement) {
						$paramAttrs = $paramElement->attributes();
						$paramValue = $paramElement->value ? (string) $paramElement->value : (string) $paramAttrs->value;
						$layout['widget']['params'][(string) $paramAttrs->name] = $paramValue;
					}
				}
				if ($filters = self::_parseFilters($element)) {
					$layout['filters'] = $filters;
				}
				break;
			
			case 'mainContentPane':
				$layout['cls'] = 'dijit.layout.ContentPane';
				if ($parent->getName() == 'zone') {
					// The numZones and zoneIndex attributes are set above
					$layout['numZones']		= (string) $parentAttrs->numZones;
					$layout['zoneIndex']	= (string) $parentAttrs->zoneIndex;
					$layout['numPortlets']  = $numChildren;
					$layout['portletIndex'] = $index;
				}
				
				if ($filters = self::_parseFilters($element)) {
					$layout['filters'] = $filters;
				}
				break;
		}
		
		// Look for sub-containers
		if (in_array($name, array('borderContainer', 'gridContainer', 'tabContainer', 'zone'))) {
			$layout['containers'] = array();
			$children			  = self::_getChildrenNodes($element);
			$numChildren		  = count($children);
			foreach ($children as $index => $child) {
				$layout['containers'][] = self::_parseNode($child, $element, $numChildren, $index);
			}
		}
		
		return $layout;
	}
	
	/**
	 * Gets children of given node
	 * 
	 * @param SimpleXMLElement $element The XML node that represents a layout element
	 * @return array
	 */
	private static function _getChildrenNodes($element)
	{
		$children = array();
		$nodes	  = $element->children();
		foreach ($nodes as $name => $node) {
			if ($node instanceof SimpleXMLElement) {
				$children[] = $node;
			} elseif (is_array($node)) {
				foreach ($node as $index => $child) {
					if ($child instanceof SimpleXMLElement) {
						$children[] = $child;
					}
				}
			}
		}
		
		return $children;
	}
	
	/**
	 * Parses the filters found in a XML node
	 * 
	 * @param SimpleXMLElement $element The XML node that represents a layout element
	 * @return array
	 */
	private static function _parseFilters($element)
	{
		if ($element->filters) {
			$filters = array();
			foreach ($element->filters->filter as $filterElement) {
				$filterAttrs = $filterElement->attributes();
				if ($filterAttrs->class) {
					$filters[] = (string) $filterAttrs->class;
				} else {
					$filters[] = ucfirst((string) $filterAttrs->module) . '_Hooks_' . ucfirst((string) $filterAttrs->name) . '_Hook';
				}
			}
			return $filters;
		}
		return null;
	}
	
	/**
	 * Generates content of layout for saving to PHP file
	 * 
	 * @param array $layout Layout data
	 * @return string
	 */
	public static function buildPhpLayout($layout)
	{
		if (!isset($layout['_depth'])) {
			$layout['_depth'] = 0;
		}
		$content	 = '';
		$indent		 = str_repeat("\t", $layout['_depth']);
		
		$properties  = $layout['properties'];
		$openDivStr  = $properties['css_class'] ? (' ' . $properties['css_class'] . '"') : '"';
		$openDivStr .= $properties['css_style'] ? (' style="' . $properties['css_style'] . '"') : '';
		$openDivStr .= $properties['id'] ? (' id="' . $properties['id'] .'"') : '';
		
		switch ($layout['cls']) {
			// Render a border container
			case 'dijit.layout.BorderContainer':
				$class    = 'appBorderContainer' . ucfirst(strtolower($layout['region']));
				$content .= $indent . '<div class="appBorderContainer ' . $class . $openDivStr . '>' . "\n";
				
				foreach ($layout['containers'] as $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::buildPhpLayout($container);
				}
				
				$content .= $indent . '</div>' . "\n";
				break;
			
			// Render a grid container
			case 'dojox.layout.GridContainer':
				$class    = 'appGridContainer-' . $layout['numZones'];
				$content .= $indent . '<div class="appGridContainer ' . $class . $openDivStr . '>' . "\n";
				
				foreach ($layout['containers'] as $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::buildPhpLayout($container);
				}
				
				$content .= $indent . '</div>' . "\n";
				break;
				
			// Render a grid zone
			case 'gridContainerZone':
				$class    = 'appGridZone-' . $layout['zoneIndex'];
				$content .= $indent . '<div class="appGridZone ' . $class . $openDivStr . '>' . "\n";
				
				foreach ($layout['containers'] as $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::buildPhpLayout($container);
				}
				
				$content .= $indent . '</div>' . "\n";
				break;
				
			// Render a tab container
			case 'dijit.layout.TabContainer':
				$content .= $indent . '<div class="appTabContainer' . $openDivStr . '>' . "\n";
				
				if (isset($layout['properties']['title']) && !empty($layout['properties']['title'])) {
					$content .= $indent . "\t" . '<div class="appTabContainerTitle">' . $layout['properties']['title'] . '</div>' . "\n";
				}
				
				// Render tab titles
				$content .= $indent . "\t" . '<div class="appTabs">' . "\n"
						 .  $indent . "\t\t" . '<ul class="appTabTitleContainer">' . "\n";
				foreach ($layout['containers'] as $index => $container) {
					$title	  = isset($container['properties']['title']) ? $container['properties']['title'] : '';
					
					if (isset($layout['selectedIndex']) && $layout['selectedIndex'] == $index) {
						$cls = ' class="appTabTitleActivated"';
					} else {
						$cls = '';
					}
					
					$content .= $indent . "\t\t\t" . '<li' . $cls . '><a>' . $title . '</a></li>' . "\n";
				}
				$content .= $indent . "\t\t" . '</ul>' . "\n";
				
				foreach ($layout['containers'] as $index => $container) {
					$container['_depth'] = $layout['_depth'] + 3;
					
					$cls      = 'appTab appTab-' . $index;
					if (isset($layout['selectedIndex']) && $layout['selectedIndex'] == $index) {
						$cls .= ' appTabActivated';
					}
					
					$content .= $indent . "\t\t" . '<div class="' . $cls . '">' . "\n";
					$content .= self::buildPhpLayout($container);
					$content .= $indent . "\t\t" . '</div>' . "\n";
				}
				
				$content .= $indent . "\t" . '</div>' . "\n"
						 .  $indent . '</div>' . "\n";
				break;
				
			// Render a widget
			case 'core.js.views.LayoutPortlet':
				$content .= $indent . '<div class="appWidgetContainer' . $openDivStr . '>' . "\n";
				
				// Render the widget
				$data = array(
					'params' => $layout['widget']['params'],
				);
				// Set the title
				if ($data['params'] && isset($properties['title'])) {
					$data['params']['title'] = $properties['title'];
				}
				
				if (isset($layout['filters']) && !empty($layout['filters'])) {
					$data['filters'] = $layout['filters'];
				}
				if (isset($properties['cache_lifetime']) && !empty($properties['cache_lifetime'])) {
					$data['cache']['lifetime'] = $properties['cache_lifetime'];
				}
				
				$content .= $indent . "\t" . '<?php' . "\n"
						 .  $indent . "\t" . 'echo $this->widget("' . $layout['widget']['name'] . '", "' . $layout['widget']['module'] .'", ' . self::_arrayToString($data) . ');' . "\n"
						 .  $indent . "\t" . '?>' . "\n"
						 .  $indent . '</div>' . "\n";
				break;
			
			// Render main content
			case 'dijit.layout.ContentPane':
				$data = array();
				if (isset($layout['filters']) && !empty($layout['filters'])) {
					$data['filters'] = $layout['filters'];
				}
				if (isset($properties['cache_lifetime']) && !empty($properties['cache_lifetime'])) {
					$data['cache']['lifetime'] = $properties['cache_lifetime'];
				}
				$content .= $indent . '<div class="appMainContentPane' . $openDivStr . '>' . "\n"
						 .  $indent . "\t" . '<?php' . "\n"
						 .	$indent . "\t" . 'echo $this->layoutContent(' . self::_arrayToString($data) . ');' . "\n"
						 .  $indent . "\t" . '?>' . "\n"
						 .  $indent . '</div>' . "\n";
				break;
		}
		
		return $content;
	}

	/**
	 * Generates a string output of given array
	 * 
	 * @param array $params The array
	 * @return string
	 */
	private static function _arrayToString($params)
	{
		$items = array();
		foreach ($params as $key => $value) {
			if (substr($key, -2) == '[]') {
				$key = substr($key, 0, -2);
			}
			$items[] = '"' . addslashes($key) . '" => ' . (is_array($value) ? self::_arrayToString($value) : '"' . addslashes($value) . '"');
		}
		$output = 'array(' . implode(', ', $items) . ')';
		return $output;
	}
	
	/**
	 * Generates content of layout for saving to XML file
	 * 
	 * @param array $layout Layout data
	 * @return string
	 */
	public static function buildXmlLayout($layout)
	{
		$layout['_depth'] = 1;
		
		return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
			  . '<layout>' . "\n"
			  . self::_buildXmlLayout($layout)
			  . '</layout>';
	}
	
	/**
	 * Generates content of layout for saving to XML file
	 * 
	 * @param array $layout Layout data
	 * @return string
	 */
	private static function _buildXmlLayout($layout)
	{
		if (!isset($layout['_depth'])) {
			$layout['_depth'] = 0;
		}
		$content = '';
		$indent	 = str_repeat("\t", $layout['_depth']);
		
		$properties  = array();
		if ($layout['properties']['css_class']) {
			$properties[] = 'cssClass="' . $layout['properties']['css_class'] . '"';
		}
		if ($layout['properties']['css_style']) {
			$properties[] = 'cssStyle="' . $layout['properties']['css_style'] . '"';
		}
		if ($layout['properties']['id']) {
			$properties[] = 'id="' . $layout['properties']['id'] . '"';
		}
		if (isset($layout['properties']['title']) && $layout['properties']['title']) {
			$properties[] = 'title="' . $layout['properties']['title'] . '"';
		}
		$propertiesStr = (count($properties) > 0) ? ' ' . implode(' ', $properties) : '';
		
		switch ($layout['cls']) {
			// Build <borderContainer> tag
			case 'dijit.layout.BorderContainer':
				$content .= $indent . '<borderContainer region="' . strtolower($layout['region']) .'"' . $propertiesStr . '>' . "\n";
				
				foreach ($layout['containers'] as $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::_buildXmlLayout($container);
				}
				
				$content .= $indent . '</borderContainer>' . "\n";
				break;
			
			// Build <gridContainer> tag
			case 'dojox.layout.GridContainer':
				$content .= $indent . '<gridContainer' . $propertiesStr . '>' . "\n";
				
				foreach ($layout['containers'] as $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::_buildXmlLayout($container);
				}
				
				$content .= $indent . '</gridContainer>' . "\n";
				break;
				
			// Build <zone> tag
			case 'gridContainerZone':
				$content .= $indent . '<zone' . $propertiesStr . '>' . "\n";
				
				foreach ($layout['containers'] as $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::_buildXmlLayout($container);
				}
				
				$content .= $indent . '</zone>' . "\n";
				break;
				
			// Build <tabContainer> tag
			case 'dijit.layout.TabContainer':
				$content .= $indent . '<tabContainer' . $propertiesStr . '>' . "\n";
				
				foreach ($layout['containers'] as $index => $container) {
					$container['_depth'] = $layout['_depth'] + 1;
					$content .= self::_buildXmlLayout($container);
				}
				
				$content .= $indent . '</tabContainer>' . "\n";
				break;
				
			// Build <widget> tag
			case 'core.js.views.LayoutPortlet':
				if (isset($layout['properties']['cache_lifetime']) && !empty($layout['properties']['cache_lifetime'])) {
					$propertiesStr .= ' cacheLifetime="' . $layout['properties']['cache_lifetime'] . '"';
				}
				
				$content .= $indent . '<widget module="' . $layout['widget']['module'] . '" name="' . $layout['widget']['name'] . '"' . $propertiesStr . '>' . "\n";
				
				// Build <filters> tag
				if (isset($layout['filters']) && !empty($layout['filters'])) {
					$content .= $indent . "\t" . '<filters>' . "\n";
					foreach ($layout['filters'] as $filter) {
						$content .= $indent . "\t\t" . '<filter class="' . $filter . '" />' . "\n"; 
					}
					$content .= $indent . "\t" . '</filters>' . "\n";
				}
				
				// Build <params> tag
				if (isset($layout['widget']['params']) && !empty($layout['widget']['params'])) {
					$content .= $indent . "\t" . '<params>' . "\n";
					foreach ($layout['widget']['params'] as $key => $value) {
						$content .= $indent . "\t\t" . '<param name="' . $key . '">' . "\n"
								 .  $indent . "\t\t\t" . '<value><![CDATA[' . $value . ']]></value>' . "\n"
								 .  $indent . "\t\t" . '</param>' . "\n";
					}
					$content .= $indent . "\t" . '</params>' . "\n";
				}
				
				$content .= $indent . '</widget>' . "\n";
				break;
			
			// Build <mainContentPane> tag
			case 'dijit.layout.ContentPane':
				if (isset($layout['properties']['cache_lifetime']) && !empty($layout['properties']['cache_lifetime'])) {
					$propertiesStr .= ' cacheLifetime="' . $layout['properties']['cache_lifetime'] . '"';
				}
				$content .= $indent . '<mainContentPane' . $propertiesStr . '>' . "\n";
				
				// Build <filters> tag
				if (isset($layout['filters']) && !empty($layout['filters'])) {
					$content .= $indent . "\t" . '<filters>' . "\n";
					foreach ($layout['filters'] as $filter) {
						$content .= $indent . "\t\t" . '<filter class="' . $filter . '" />' . "\n"; 
					}
					$content .= $indent . "\t" . '</filters>' . "\n";
				}
				
				$content .=  $indent . '</mainContentPane>' . "\n";
				break;
		}
		
		return $content;
	}
}
