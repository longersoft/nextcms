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
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define routes for managing attachments
 * 
 * @return array
 */
return array(
	////////// FRONTEND ACTIONS //////////
	// Download attachment
	'file_attachment_download' => array(
		'type'	   => 'Zend_Controller_Router_Route_Regex',
		'route'	   => 'file/attachment/download/(\w+)',
		'reverse'  => 'file/attachment/download/%s',
		'map'	   => array(
			'1' => 'attachment_id',
		),
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'attachment',
			'action'	 => 'download',
			'frontend'	 => array(
				'enabled'		 => true,
				'translationKey' => 'attachment.download.title',
			),
			'permalink'  => array(
				'enabled'		 => true,
				'translationKey' => 'attachment.download.title',
				'params'		 => array(
					'attachment_id' => array(
						'name'	   => 'attachment_id',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'hash' => array(
						'name'	   => 'hash',
						'regex'	   => '(\w+)',
						'reverse'  => '%s',
						'required' => true,
					),
					'slug' => array(
						'name'	   => 'slug',
						'regex'	   => '([\w-_]+)',
						'reverse'  => '%s',
						'required' => false,
					),
				),
				'type'		 => 'default',
				'default'	 => 'file/attachment/download/{attachment_id}',
				'predefined' => array(
					'file/attachment/download/{slug}.html',
					'file/attachment/download/{hash}.html',
					'file/attachment/download/{attachment_id}-{slug}.html',
				),
			),
		),
	),

	////////// BACKEND ACTIONS //////////
	// Add new attachment
	'file_attachment_add' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/attachment/add',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'attachment',
			'action'	 => 'add',
		),
	),
	
	// Delete attachment
	'file_attachment_delete' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/attachment/delete',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'attachment',
			'action'	 => 'delete',
		),
	),
	
	// Edit attachment
	'file_attachment_edit' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/attachment/edit',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'attachment',
			'action'	 => 'edit',
		),
	),
	
	// List attachments
	'file_attachment_list' => array(
		'type'	   => 'Zend_Controller_Router_Route_Static',
		'route'	   => '{adminPrefix}/file/attachment/list',
		'defaults' => array(
			'module'	 => 'file',
			'controller' => 'attachment',
			'action'	 => 'list',
		),
	),
);
