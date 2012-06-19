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
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Provide some additional methods related to Dojo settings
 */
class Core_View_Helper_DojoConfig
{
	/**
	 * Dojo theme
	 * 
	 * @var string
	 */
	private $_theme;
	
	/**
	 * Gets this view helper instance
	 * 
	 * @return Core_View_Helper_DojoConfig
	 */
	public function dojoConfig()
	{
		return $this;
	}
	
	/**
	 * Gets current Dojo theme.
	 * In the main layout of the back-end, we can set the dojo them as follow:
	 * 		<body class="<?php echo $this->dojoConfig()->getTheme(); ?>">
	 * The value is taken from value of dojo_theme setting.
	 * 
	 * @return string
	 */
	public function getTheme()
	{
		// The default theme is "claro".
		// There is another way to set the theme for body tag.
		// That is, we create a const named APP_DOJO_THEME inside the Init plugin
		//		class Core_Controllers_Plugins_Init extends Zend_Controller_Plugin_Abstract 
		//		{
		//			public function preDispatch(Zend_Controller_Request_Abstract $request) 
		//			{
		//				$view->APP_DOJO_THEME = Core_Services_Config::get('core', 'dojo_theme', 'claro');
		//			}
		//		}
		// and use this constant in layout:
		//		echo $this->APP_DOJO_THEME;
		// With this approach, we have to hit the DB for every requests.
		// Meanwhile, most of the back-end requests will be Ajax, therefore 
		// I call it only one time at the main layout view script.
		if ($this->_theme == null) {
			Core_Services_Db::connect('slave');
			$this->_theme = Core_Services_Config::get('core', 'dojo_theme', 'claro');
		}
		return $this->_theme;
	}
}
