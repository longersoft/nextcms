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
 * @subpackage	tasks
 * @since		1.0
 * @version		2011-12-09
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the AutoBackup task
 * 
 * @return array
 */
return array(
	'autobackup' => array(
		'translationKey' => '_permission.description',
		'description'	 => 'Backups the MySQL database automatically',
		'actions'		 => array(
			'delete' => array(
				'translationKey' => '_permission.actions.delete',
				'description'	 => 'Delete a SQL file',
			),
			'download' => array(
				'translationKey' => '_permission.actions.download',
				'description'	 => 'Download a SQL file',
			),
			'view' => array(
				'translationKey' => '_permission.actions.view',
				'description' 	 => 'View SQL files',
			),
		),
	),
);
