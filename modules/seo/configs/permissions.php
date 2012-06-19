<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		seo
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Seo module
 * 
 * @return array
 */
return array(
	// Sitemap Builder
	'sitemap' => array(
		'translationKey' => '_permission.sitemap.description',
		'description'	 => 'Sitemap Builder',
		'actions'		 => array(
			'build' => array(
				'translationKey' => '_permission.sitemap.actions.build',
				'description'	 => 'Build sitemap',
			),
		),
	),
);
