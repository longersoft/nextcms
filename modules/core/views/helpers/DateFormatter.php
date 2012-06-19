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
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Provides methods to format date
 */
class Core_View_Helper_DateFormatter
{
	/**
	 * @var array
	 */
	private static $_DIFF_FORMAT = array(
		'DAY'			=> '%s days ago',
		'DAY_HOUR'		=> '%s days %s hours ago',
		'HOUR'			=> '%s hours ago',
		'HOUR_MINUTE'	=> '%s hours %s minute ago',
		'MINUTE'		=> '%s minutes ago',
		'MINUTE_SECOND'	=> '%s minutes %s seconds ago',
		'SECOND'		=> '%s seconds ago',
	);
	
	/**
	 * Gets view helper instance
	 * 
	 * @return Core_View_Helper_DateFormatter
	 */
	public function dateFormatter()
	{
		return $this;
	}
	
	/**
	 * Gets the diff between given timestamp and now
	 * 
	 * @param int $timestamp
	 * @param array $formats
	 * @return string
	 */
	public function diff($timestamp, $formats = null) 
	{
		if ($formats == null) {
			$formats = self::$_DIFF_FORMAT;
		}
		$seconds = time() - $timestamp;
		$minutes = floor($seconds / 60);
		$hours 	 = floor($minutes / 60);
		$days 	 = floor($hours / 24);
		
		switch (true) {
			case ($days > 0):
				return sprintf($formats['DAY'], $days);
				break;
			case ($hours > 0):
				return ($minutes > 0 && ($minutes - $hours * 60) > 0)
						? sprintf($formats['HOUR_MINUTE'], $hours, $minutes - $hours * 60)
						: sprintf($formats['HOUR'], $hours);
				break;
			case ($hours == 0):
				return (($seconds - $minutes * 60) > 0 && $minutes > 0)
						? sprintf($formats['MINUTE_SECOND'], $minutes, $seconds - $minutes * 60)
						: sprintf($formats['SECOND'], $seconds);
				break;
		}
	}
}
