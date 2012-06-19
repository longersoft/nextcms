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
 * @subpackage	models
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents hook target
 */
class Core_Models_Target extends Core_Base_Models_Entity
{
	/**
	 * Target's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'target_id'		=> null,
		'target_module' => null,
		'target_name'	=> null,
		'hook_module'	=> null,
		'hook_name'		=> null,
		'hook_method'	=> null,
		'params'		=> null,
		'echo'			=> 0,
	);
}
