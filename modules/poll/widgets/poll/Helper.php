<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-03-07
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Poll_Widgets_Poll_Helper
{
	/**
	 * Gets helper instance
	 * 
	 * @return Poll_Widgets_Poll_Helper
	 */
	public function helper()
	{
		return $this;
	}
	
	/**
	 * Generates a random HEX color
	 * 
	 * @return string
	 */
	public function generateRandomColor()
	{
		return sprintf('#%02X%02X%02X', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	}
}
