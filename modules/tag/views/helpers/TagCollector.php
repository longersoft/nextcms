<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	views
 * @since		1.0
 * @version		2012-01-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_View_Helper_TagCollector extends Zend_View_Helper_Abstract
{
	/**
	 * Shows a box to contain tags dragged from the Tags provider
	 * 
	 * @param string $container Id of box container
	 * @param string $inputName Name of hidden inputs which have value of tag's Ids
	 * @param array $tags Array of tags, each item is an instance of Tag_Models_Tag
	 * @return string
	 */
	public function tagCollector($container, $inputName = 'tags[]', $tags = array())
	{
		Core_Services_Db::connect('master');
		
		$this->view->assign(array(
			'tagContainer' => $container,
			'tagInputName' => $inputName,
			'tags'		   => $tags,
		));
		$this->view->translator()->setLanguageDir('/modules/tag/languages');
		$output = $this->view->render('_partial/_tagCollector.phtml');
		
		// Reset the language dir
		$this->view->translator()->setLanguageDir(null);
		
		return $output;
	}
}
