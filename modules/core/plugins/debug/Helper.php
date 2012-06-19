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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Debug_Helper
{
	/**
	 * @return Core_Plugins_Debug_Helper
	 */
	public function helper()
	{
		return $this;
	}
	
	/**
	 * Formats file zie
	 * 
	 * @param int $size File size
	 * @return string File size in readable unit (KB, MB, GB, etc.)
	 */
	public function formatSize($size)
	{
		if ($size == 0) {
			return '0 byte';
		}
		switch (true) {
			case ($size >= 1073741824):
				return number_format($size / 1073741824, 2, '.', '') . ' Gb';
			case ($size >= 1048576):
				return number_format($size / 1048576, 2, '.', '') . ' Mb';
			case ($size >= 1024):
				return number_format($size / 1024, 0) . ' Kb';
			default:
				return number_format($size, 0) . ' bytes';
		}
		return size;
	}
	
	/**
	 * Formats the variables
	 * 
	 * @param array $vars The array
	 * @param string $cssClass The CSS class that is added to the container showing the variables
	 * @param string $newLine The new line character
	 * @return string
	 */
	public function formatVars($vars, $cssClass, $newLine = '<br />')
	{
		$return = '<div class="' . $cssClass . '">';
		foreach ($vars as $key => $value) {
			$return .= htmlspecialchars($key) . ' => ';
			switch (true) {
				case is_bool($value):
					$return .= ($value ? 'true' : 'false') . $newLine;
					break;  
				case is_numeric($value):
					$return .= $value . $newLine;
					break;
				case is_string($value):
					$return .=  htmlspecialchars($value) . $newLine;
					break;
				case is_array($value):
					$return .= count($value) == 0 ? ('[]' . $newLine) : $this->formatVars($value, $cssClass);
					break;
				case is_object($value):
					$return .= 'object(' . get_class($value) . ')' . $newLine;
					break;
				case is_null($value):
					$return .= 'NULL' . $newLine;
					break;
			}
		}
		
		$return .= '</div>';
		return $return;
	}
}
