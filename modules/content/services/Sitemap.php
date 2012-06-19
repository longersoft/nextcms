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
 * @subpackage	services
 * @since		1.0
 * @version		2012-04-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Services_Sitemap
{
	/**
	 * URL to ping search engines
	 * %s will be replaced with the URL of sitemap
	 * 
	 * @var array
	 */
	public static $PING_URLS = array(
		'google.com' => 'http://www.google.com/webmasters/sitemaps/ping?sitemap=%s',
		'bing.com'	 => 'http://www.bing.com/webmaster/ping.aspx?siteMap=%s',
		'ask.com'	 => 'http://submissions.ask.com/ping?sitemap=%s',
	);
	
	/**
	 * Adds sitemap declaration to robots.txt
	 * It should be called after installing the module
	 * 
	 * @return void
	 */
	public static function addDeclaration()
	{
		// Get the view instance
		$view		 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$sitemap	 = $view->serverUrl() . $view->url(array(), 'content_sitemap_index');
		$declaration = 'Sitemap: ' . $sitemap;
		
		$file = APP_ROOT_DIR . DS . 'robots.txt';
		if (!file_exists($file)) {
			@file_put_contents($file, $declaration);
		} else {
			$lines = file($file);
			foreach ($lines as $index => $string) {
				if (trim($string) == $declaration) {
					unset($lines[$index]);
				}
			}
			array_unshift($lines, $declaration . "\n");
			@file_put_contents($file, implode('', $lines));
		}
	}
	
	/**
	 * Removes sitemap declaration from robots.txt
	 * It should be called after uninstalling the module
	 * 
	 * @return void
	 */
	public static function removeDeclaration()
	{
		// Get the view instance
		$view		 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$sitemap	 = $view->serverUrl() . $view->url(array(), 'content_sitemap_index');
		$declaration = 'Sitemap: ' . $sitemap;
		
		$file = APP_ROOT_DIR . DS . 'robots.txt';
		if (!file_exists($file)) {
			return;
		}
		$lines = file($file);
		foreach ($lines as $index => $string) {
			if (trim($string) == $declaration) {
				unset($lines[$index]);
			}
		}
		@file_put_contents($file, implode('', $lines));
	}
	
	/**
	 * Gets the sitemap file containing the link to articles that are activated 
	 * in given year and month
	 * 
	 * @param int $year The year
	 * @param int $month The month
	 * @return string
	 */
	public static function getArchiveFile($year, $month)
	{
		return APP_ROOT_DIR . DS . 'temp' . DS . 'cache' . DS . 'sitemap_content_articles_' . $year . '_' . $month . '.xml';
	}
	
	/**
	 * Gets the sitemap file containing the link to archive sitemap
	 * 
	 * @return string
	 */
	public static function getFile()
	{
		return APP_ROOT_DIR . DS . 'temp' . DS . 'cache' . DS . 'sitemap_content_articles.xml';
	}
	
	/**
	 * Gets pages from a XML file to add to a navigation container later
	 * 
	 * @param string $file The path of XML file
	 * @param string $pageUrl
	 * @param string $elementTag Can be "url" or "sitemap"
	 * @return array
	 */
	public static function getPages($file, $pageUrl, $elementTag)
	{
		if (!file_exists($file)) {
			return array();
		}
		$xml   = simplexml_load_file($file);
		$pages = array();
		foreach ($xml->$elementTag as $element) {
			$loc = (string) $element->loc;
			if ($loc && $loc != $pageUrl) {
				$pages[] = array(
					'uri'		 => $loc,
					'loc'		 => $loc,
					'lastmod'	 => (string) $element->lastmod,
					'changefreq' => (string) $element->changefreq,
				);
			}
		}
		return $pages;
	}
	
	/**
	 * Updates sitemap after activating an article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @param string $url The article URL
	 * @return void
	 */
	public static function update($article, $url)
	{
		if (!($article instanceof Content_Models_Article)) {
			return;
		}
		$activatedDate = $article->activated_date ? $article->activated_date : date('Y-m-d H:i:s');
		list($year, $month) = explode('-', date('Y-m', strtotime($activatedDate)));
		
		// Get the view instance
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		// The file to cache sitemap
		$file = self::getArchiveFile($year, $month);
		
		// Add a sitemap item
		$container = new Zend_Navigation();
		$container->addPage(array(
			'uri'	  => $url,
			'loc'	  => $url,
			'lastmod' => $activatedDate,
		));
		$container->addPages(self::getPages($file, $url, 'url'));
		
		// Save sitemap to file
		$xml = $view->navigation($container)
					->sitemap()
					->setFormatOutput(true)
					->render();
		@file_put_contents($file, $xml);
		
		$sitemapUrl = $view->serverUrl() . $view->url(array(
												'year'	=> $year,
												'month' => $month,
											), 'content_sitemap_view');
		// Update the sitemap index
		$sitemapIndexFile = self::getFile();
		$container->removePages();
		$container->addPage(array(
			'uri'	  => $sitemapUrl,
			'loc'	  => $sitemapUrl,
			'lastmod' => $activatedDate,
		));
		$container->addPages(self::getPages($sitemapIndexFile, $sitemapUrl, 'sitemap'));
		
		$xml = $view->navigation($container)
					->sitemapIndex()
					->setUseSitemapValidators(false)
					->setFormatOutput(true)
					->render();
		@file_put_contents($sitemapIndexFile, $xml);

		// Ping search engines to inform of updating the sitemap
		self::ping($sitemapUrl);
	}
	
	/**
	 * Pings search engines
	 * 
	 * @param string $sitemapUrl The URL of sitemap
	 * @return void
	 */
	public static function ping($sitemapUrl)
	{
		$sitemapUrl = urlencode($sitemapUrl);
		$client		= new Zend_Http_Client();
		// See http://framework.zend.com/manual/en/zend.http.client.html#zend.http.client.configuration
		$client->setConfig(array(
			'adapter'	 => 'Zend_Http_Client_Adapter_Socket',
			'timeout'	 => 10,
			'persistent' => true,
		));
		
		foreach (self::$PING_URLS as $searchEngine => $pingUrl) {
			$url = sprintf($pingUrl, $sitemapUrl);
			$client->setUri($url)
				   ->setMethod(Zend_Http_Client::GET)
				   ->request();
		}
	}
}
