<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	services
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Services_Comparator_Chain
{
	/**
	 * Array of comparators
	 * 
	 * @var array
	 */
	private $_comparators = array();
	
	/**
	 * Adds a comparator to the chain
	 * 
	 * @param mixed $comparator
	 * @return File_Services_Comparator_Chain
	 */
	public function addComparator($comparator)
	{
		$this->_comparators[] = $comparator;
		return $this;
	}
	
	/**
	 * Compares two files
	 * 
	 * @param array $firstFile
	 * @param array $secondFile
	 * @return int -1, 0, or 1
	 */
	public function compare($firstFile, $secondFile)
	{
		$result = 0;
		foreach ($this->_comparators as $comparator) {
			$result = $comparator->compare($firstFile, $secondFile);
			if ($result != 0) {
				break;
			}
		}
		return $result;
	}
}
