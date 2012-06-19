<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	views
 * @since		1.0
 * @version		2011-10-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Category_View_Helper_CategorySelect extends Zend_View_Helper_Abstract
{
	/**
	 * Shows a select box containing list of categories
	 * 
	 * @param string $module The name of module
	 * @param string $language The language
	 * @param array $params Contains the data to render the select box, including:
	 * - name: Name of select box
	 * - selected: Id of selected category
	 * - disabled: true or false
	 * @return string
	 */
	public function categorySelect($module, $language, $params = array())
	{
		Core_Services_Db::connect('master');
		
		$this->view->assign(array(
			'categories' => Category_Services_Category::getTree($module, $language),
			'params'	 => array_merge(array(
				'name'	   => 'category',
				'selected' => null,
				'disabled' => false,
			), $params),
		));
		return $this->view->render('_partial/_categorySelect.phtml');
	}
}
