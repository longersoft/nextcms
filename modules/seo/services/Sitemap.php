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
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Seo_Services_Sitemap
{
	/**
	 * Gets sitemap items
	 * 
	 * @return array
	 */
	public static function getItems()
	{
		$file = APP_ROOT_DIR . DS . 'sitemap.xml';
		if (!file_exists($file)) {
			return array();
		}
		$items = array();
		$xml   = simplexml_load_file($file);
		foreach ($xml->url as $url) {	
			$items[] = array(
				'link'			=> (string) $url->loc,
				'frequency'		=> (string) $url->changefreq,
				'priority'		=> (string) $url->priority,
				'last_modified' => (string) $url->lastmod,
			);
		}
		return $items;
	}
	
	/**
	 * Saves the sitemap data to file
	 * 
	 * @param array $items Each item contains data of sitemap item:
	 * link, frequency, priority, last_modified
	 * @return bool
	 */
	public static function save($items)
	{
		if (!is_array($items) || count($items) == 0) {
			return false;
		}
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
				. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL
				. '	   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL
				. '	   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
		foreach($items as $item) {
			$output .= '<url>' . PHP_EOL
					. '<loc>' . $item['link'] . '</loc>' . PHP_EOL
					. '<priority>' . $item['priority'] . '</priority>' . PHP_EOL
					. '<changefreq>' . $item['frequency'] . '</changefreq>' . PHP_EOL;
			if (isset($item['last_modified']) && !empty($item['last_modified'])) {
				$output .= '<lastmod>' . $item['last_modified'] . '</lastmod>' . PHP_EOL;
			}
			$output .= '</url>' . PHP_EOL;
		}
		$output .= '</urlset>';
		
		// Write to file
		$file = APP_ROOT_DIR . DS . 'sitemap.xml';
		$f	  = fopen($file, 'w');
		fwrite($f, $output);
		fclose($f);
		
		return true;
	}
}
