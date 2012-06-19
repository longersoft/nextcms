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

class Category_View_Helper_CategoryCheckboxTree extends Zend_View_Helper_Abstract
{
	/**
	 * Shows a category tree with checkbox in each item
	 * 
	 * @param string $module The name of module
	 * @param string $language The language
	 * @param array $params Contains the following members:
	 * - id: Id of tree container
	 * - name: Name of checkboxes
	 * - selected: Ids of selected categories
	 * @return string
	 */
	public function categoryCheckboxTree($module, $language, $params = array())
	{
		$this->view->assign(array(
			'module'   => $module,
			'language' => $language,
			'params'   => array_merge(array(
				'name'	   => 'categories[]',
				'selected' => null,
				'disabled' => false,
			), $params),
		));
		return $this->view->render('_partial/_categoryCheckboxTree.phtml');
	}
}
