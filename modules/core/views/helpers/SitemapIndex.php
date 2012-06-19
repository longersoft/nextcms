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
 * @version		2011-12-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_View_Helper_SitemapIndex extends Zend_View_Helper_Navigation_Sitemap
{
	public function sitemapindex(Zend_Navigation_Container $container = null)
	{
		if (null !== $container) {
			$this->setContainer($container);
		}
		return $this;
	}

	/**
	 * @see Zend_View_Helper_Navigation_Sitemap::getDomSitemap()
	 */
	public function getDomSitemap(Zend_Navigation_Container $container = null)
	{
		if (null === $container) {
			$container = $this->getContainer();
		}

		// Check if we should validate using our own validators
		if ($this->getUseSitemapValidators()) {
			require_once 'Zend/Validate/Sitemap/Changefreq.php';
			require_once 'Zend/Validate/Sitemap/Lastmod.php';
			require_once 'Zend/Validate/Sitemap/Loc.php';
			require_once 'Zend/Validate/Sitemap/Priority.php';

			// Create validators
			$locValidator        = new Zend_Validate_Sitemap_Loc();
			$lastmodValidator    = new Zend_Validate_Sitemap_Lastmod();
			$changefreqValidator = new Zend_Validate_Sitemap_Changefreq();
			$priorityValidator   = new Zend_Validate_Sitemap_Priority();
		}

		// Create document
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = $this->getFormatOutput();

		// ... and sitemapindex  (root) element
		$sitemapIndex = $dom->createElementNS(parent::SITEMAP_NS, 'sitemapindex');
		$dom->appendChild($sitemapIndex);

		// Create iterator
		$iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

		$maxDepth = $this->getMaxDepth();
		if (is_int($maxDepth)) {
			$iterator->setMaxDepth($maxDepth);
		}
		$minDepth = $this->getMinDepth();
		if (!is_int($minDepth) || $minDepth < 0) {
			$minDepth = 0;
		}

		// Iterate container
		foreach ($iterator as $page) {
			if ($iterator->getDepth() < $minDepth || !$this->accept($page)) {
				// page should not be included
				continue;
			}

			// Get absolute url from page
			if (!$url = $this->url($page)) {
				// skip page if it has no url (rare case)
				continue;
			}

			// Create sitemap node for this page
			$sitemapNode = $dom->createElementNS(parent::SITEMAP_NS, 'sitemap');
			$sitemapIndex->appendChild($sitemapNode);

			if ($this->getUseSitemapValidators() &&
			!$locValidator->isValid($url)) {
				require_once 'Zend/View/Exception.php';
				$e = new Zend_View_Exception(sprintf('Encountered an invalid URL for Sitemap XML: "%s"', $url));
				$e->setView($this->view);
				throw $e;
			}

			// Put url in 'loc' element
			$sitemapNode->appendChild($dom->createElementNS(parent::SITEMAP_NS, 'loc', $url));

			// Add 'lastmod' element if a valid lastmod is set in page
			if (isset($page->lastmod)) {
				$lastmod = strtotime((string) $page->lastmod);

				// Prevent 1970-01-01...
				if ($lastmod !== false) {
					$lastmod = date('c', $lastmod);
				}

				if (!$this->getUseSitemapValidators() ||
				$lastmodValidator->isValid($lastmod)) {
					$sitemapNode->appendChild(
						$dom->createElementNS(parent::SITEMAP_NS, 'lastmod', $lastmod)
					);
				}
			}

			// Add 'changefreq' element if a valid changefreq is set in page
			if (isset($page->changefreq)) {
				$changefreq = $page->changefreq;
				if (!$this->getUseSitemapValidators() ||
				$changefreqValidator->isValid($changefreq)) {
					$sitemapNode->appendChild(
						$dom->createElementNS(parent::SITEMAP_NS, 'changefreq', $changefreq)
					);
				}
			}

			// Add 'priority' element if a valid priority is set in page
			if (isset($page->priority)) {
				$priority = $page->priority;
				if (!$this->getUseSitemapValidators() ||
				$priorityValidator->isValid($priority)) {
					$sitemapNode->appendChild(
						$dom->createElementNS(parent::SITEMAP_NS, 'priority', $priority)
					);
				}
			}
		}

		// Validate using schema if specified
		if ($this->getUseSchemaValidation()) {
			if (!@$dom->schemaValidate(parent::SITEMAP_XSD)) {
				require_once 'Zend/View/Exception.php';
				$e = new Zend_View_Exception(sprintf('Sitemap is invalid according to XML Schema at "%s"', parent::SITEMAP_XSD));
				$e->setView($this->view);
				throw $e;
			}
		}

		return $dom;
	}
}
