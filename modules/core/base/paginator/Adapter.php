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
 * @subpackage	base
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Paginator_Adapter extends Zend_Paginator_Adapter_Iterator
{
	public function __construct(Iterator $iterator, $count)
	{
		parent::__construct($iterator);
		
		// This is simple trick that allow us to set the total number of items
		$this->_count = $count;
	}
}
