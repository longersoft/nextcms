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

/**
 * Provides the method to compare two file on given field
 */
class File_Services_Comparator_Field
{
	/**
	 * The field's name
	 * 
	 * @var string
	 */
	private $_field;
	
	/**
	 * @var boolean
	 */
	private $_descending = false;
	
	public function __construct($field, $descending = false)
	{
		$this->_field	   = $field;
		$this->_descending = $descending;
	}
	
	/**
	 * Compares two files on given field
	 * 
	 * @param array $firstFile The array contains the information of first file
	 * @param array $secondFile The array contains the information of second file
	 * @return int -1, 0, 1
	 */
	public function compare($firstFile, $secondFile)
	{
		$a = $firstFile[$this->_field];
		$b = $secondFile[$this->_field];

		$result = 0;
		switch (true) {
			case (is_string($a) && is_string($b)):
				$result = strcmp($a, $b);
				break;
			case ($a > $b || $a === null):
				$result = 1;
				break;
			case ($a < $b || $b === null):
				$result = -1;
				break;
		}
		
		if ($this->_descending) {
			$result = $result * -1;
		}
		
		switch (true) {
			case ($result > 0):
				return 1;
			case ($result < 0):
				return -1;
			default:
				return 0;
		}
	}
}
