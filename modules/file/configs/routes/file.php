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
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for uploading, browsing files on the local server
 * 
 * @return array
 */
return array(
	////////// BACKEND ACTIONS //////////
	// Upload file. The thumbnails will be generated automatically in various sizes
	'file_file_upload' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/file/upload',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'file',
			'action'	 => 'upload',
		),
	),
);
