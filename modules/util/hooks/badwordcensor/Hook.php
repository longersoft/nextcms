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
 * @version		2012-04-06
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Util_Hooks_Badwordcensor_Hook extends Core_Base_Extension_Hook 
	implements Zend_Filter_Interface
{
	/**
	 * Replaces bad words
	 * 
	 * @see Zend_Filter_Interface::filter()
	 * @param string $value The original content
	 * @return string
	 */
	public function filter($value)
	{
		Core_Services_Db::connect('slave');
		
		$options = Core_Services_Hook::getOptionsByInstance($this);
		if ($options == null || !isset($options['bad_words']) || !$options['bad_words']) {
			return $value;
		}
		
		$badWords = explode(',', $options['bad_words']);
		foreach ($badWords as $word) {
			// Keep the first character
			$value = str_replace($word, substr($word, 0, 1) . $options['replace_with'], $value);
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
		Core_Services_Db::connect('master');
		
		$options = Core_Services_Hook::getOptionsByInstance($this);
		$this->view->assign(array(
			'badWords'	  => $options ? $options['bad_words'] : '',
			'replaceWith' => $options ? $options['replace_with'] : '*****',
		));
	}
	
	/**
	 * Saves the hook's options
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$options = array(
			'bad_words'	   => $request->getParam('bad_words'),
			'replace_with' => $request->getParam('replace_with'),
		);
		$result = Core_Services_Hook::setOptionsForInstance($this, $options);
		return $result ? 'true' : 'false';
	}
}
