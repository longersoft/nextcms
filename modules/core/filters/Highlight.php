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
 * @subpackage	filters
 * @since		1.0
 * @version		2012-05-27
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Filters_Highlight implements Zend_Filter_Interface
{
	/**
	 * Singleton instance of the filter
	 * 
	 * @var Core_Filters_Highlight
	 */
	private static $_instance;
	
	/**
	 * The name of cookie that stores the keyword
	 * 
	 * @var string
	 */
	const COOKIE_KEYWORD = 'Core_Filters_Highlight_Keyword';
	
	/**
	 * The searching keyword
	 * 
	 * @var string
	 */
	protected $_keyword;

	/**
	 * Private constructor
	 * 
	 * @return void
	 */
	private function __construct()
	{
	}
	
	/**
	 * Gets the instance of filter
	 * 
	 * @return Core_Filters_Highlight
	 */
	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Sets the keyword
	 * 
	 * @param string $keyword
	 * @return string
	 */
	public function setKeyword($keyword)
	{
		if (!$keyword) {
			return;
		}
		
		$this->_keyword = $keyword;
		// Store the searching keyword in cookie, to get it in other pages
		setcookie(self::COOKIE_KEYWORD, Core_Services_Encryptor::encrypt($keyword), time() + 1800);
		return $keyword;
	}
	
	/**
	 * Highlights the keyword found in given content.
	 * It will search for the keyword and wrap it in a "span" tag: <span class="coreFiltersHighlight">$keyword</span>
	 * 
	 * Usage:
	 * - In controller action or module bootstrap:
	 * <pre>
	 * Core_Base_Hook_Registry::getInstance()->register('Content_FilterArticleTitle', array(Core_Filters_Highlight::getInstance(), 'filter'));
	 * </pre>
	 * - In view script:
	 * <pre>
	 * <?php echo Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', '<h1 class="title">' . $article->title . '</h1>'); ?>
	 * </pre>
	 * 
	 * It is recommended that when apply this filter to the title of something, such as the title of an article,
	 * you should pass the tag showing the title instead of title only:
	 * 
	 * SHOULD:
	 * <pre>
	 * 	<?php echo Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', '<h1 class="title">' . $article->title . '</h1>'); ?>
	 * 
	 * 	<div class="title">
	 * 		<?php echo Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', $article->title); ?>
	 * 	</div>
	 * 
	 * 	<?php echo Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', '<h2><a href="' . $article->getViewUrl() . '" title="' . addslashes($article->title) . '">' . $article->title . '</a></h2>'); ?>
	 * </pre>
	 * 
	 * SHOULD NOT:
	 * <pre>
	 * 	<h1 class="title">
	 * 		<?php echo Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', $article->title); ?>
	 *	</h1>
	 *
	 * 	<h2>
	 * 		<a href="<?php echo $article->getViewUrl(); ?>" title="<?php echo addslashes($article->title); ?>">
	 * 			<?php echo Core_Base_Hook_Registry::getInstance()->executeFilter('Content_FilterArticleTitle', $article->title); ?>
	 *		</a>
	 *	</h2>
	 * </pre>
	 * 
	 * because in this case, the method will return a string wrapped in a P tag which cannot be placed inside a heading (H1-H6) tag. 
	 * 
	 * @param string $value
	 * @see Zend_Filter_Interface::filter()
	 * @return string
	 */
	public function filter($value)
	{
		$keyword = null;
		if ($this->_keyword) {
			$keyword = $this->_keyword; 
		} elseif (isset($_COOKIE[self::COOKIE_KEYWORD]) && !empty($_COOKIE[self::COOKIE_KEYWORD])) {
			$keyword = Core_Services_Encryptor::decrypt($_COOKIE[self::COOKIE_KEYWORD]);	
		}
		
		if (!$keyword) {
			return $value;
		}
		
		// Sanitize the keyword
		$keyword = strip_tags($keyword);
		$keyword = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $keyword);
		if ($keyword == '') {
			return $value;
		}
		
		try {
			$dom = new DomDocument();
			@$dom->loadHtml($value);
			$xpath	  = new DomXpath($dom);
			$upper	  = strtoupper(addslashes($keyword));
			$lower	  = strtolower(addslashes($keyword));
			// Support i-case sensitive using translate method provided by XPath 1.0
			$query	  = '//*[contains(translate(., "' . $upper . '", "' . $lower . '"), "' . $lower . '")]';
			$elements = $xpath->query($query);
			
			foreach ($elements as $element) {
				foreach ($element->childNodes as $child) {
					if (!$child instanceof DomText) {
						continue;
					}
					$fragment = $dom->createDocumentFragment();
					$text	  = $child->textContent;
					while (($pos = stripos($text, $keyword)) !== false) {
						$fragment->appendChild(new DomText(substr($text, 0, $pos)));
						$word	   = substr($text, $pos, strlen($keyword));
						
						// Create a SPAN element
						$highlight = $dom->createElement('span');
						$highlight->appendChild(new DomText($word));
						$highlight->setAttribute('class', 'coreFiltersHighlight');
						$fragment->appendChild($highlight);
						$text = substr($text, $pos + strlen($keyword));
					}
					if (!empty($text)) {
						$fragment->appendChild(new DomText($text));
					}
					// Replace the text node element with the new one that contains the SPAN tag
					$element->replaceChild($fragment, $child);
				}
			}
			return $dom->saveXml($dom->getElementsByTagName('body')->item(0)->firstChild);
		} catch (Exception $ex) {
			return preg_replace('/(?![^<>]*>)' . preg_quote($keyword, '/') . '/i', '<span class="coreFiltersHighlight">' . $keyword . '</span>', $value);
		}
		
		return $value;
	}
}
