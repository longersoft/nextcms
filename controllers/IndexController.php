<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	controllers
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class IndexController extends Zend_Controller_Action
{
	/**
	 * @return void
	 */
	public function indexAction()
	{
		// It will never be requested because the app does not use default route.
		// The reasons why you see it here are:
		// - It is friendly to the ZF beginner users,
		// - I have to set the default controller directory
		// in the entry point of the app (index.php):
		// 		$options = array(
		// 			'bootstrap' => array(
		// 				'path' 		=> ...,
		// 				'class' 	=> ...,
		// 			),
		// 			'resources' => array(
		// 				'frontController' => array(
		//					'controllerDirectory' => APP_ROOT_DIR . DS . 'controllers',
		// 					'moduleDirectory' 	  => ...,
		//			),
		//		);
		// - The GIT system does not allow me to commit an empty directory, such as
		// this "controllers" directory without this file.
	}
}
