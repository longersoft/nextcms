<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		util
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-05-30
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Hooks_Emoticon_Hook extends Core_Base_Extension_Hook
	implements Zend_Filter_Interface
{
	public function __construct()
	{
		parent::__construct();
		if (!Zend_Layout::getMvcInstance() || Zend_Layout::getMvcInstance()->getLayout() != 'admin') {
			$options = Core_Services_Hook::getOptions('emoticon', 'util');
			if ($options) {
				$this->view->style()->appendStylesheet($this->view->APP_ROOT_URL . '/modules/util/hooks/emoticon/skins/' . $options['skin'] . '/styles.css');
			}
		}
	}
	
	/**
	 * Replaces special characters with icons
	 * 
	 * @see Zend_Filter_Interface::filter()
	 * @param string $value The original content
	 * @return string
	 */
	public function filter($value)
	{
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Hook::getOptionsByInstance($this);
		if ($options == null) {
			return $value;
		}
		
		foreach ($options['maps'] as $icon => $text) {
			foreach ($text as $str) {
				$value = str_replace($str, '<span class="utilHooksEmoticonIcon utilHooksEmoticon' . $icon . '">&nbsp;</span>', $value);
			}
		}
		return $value;
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$this->view->assign('skins', Core_Base_File::getSubDirectories(dirname(__FILE__) . DS . 'skins'));
	}
	
	/**
	 * Saves the hook's options
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$hook	 = Core_Services_Hook::getHookInstance('emoticon', 'util');
		$options = array(
			'skin' => $this->getRequest()->getParam('skin'),
			'maps' => $hook->options['maps'],
		);
		$result = Core_Services_Hook::setOptionsForInstance($this, $options);
		return $result ? 'true' : 'false';
	}
}
