<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	configs
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Ad module
 * 
 * @return array
 */
return array(
	// Banners manager
	'banner' => array(
		'translationKey' => '_permission.banner.description',
		'description'	 => 'Manage banners',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.banner.actions.activate',
				'description'	 => 'Activate banner',
			),
			'add' => array(
				'translationKey' => '_permission.banner.actions.add',
				'description'	 => 'Add new banner',
			),
			'delete' => array(
				'translationKey' => '_permission.banner.actions.delete',
				'description'	 => 'Delete banner',
			),
			'edit' => array(
				'translationKey' => '_permission.banner.actions.edit',
				'description'	 => 'Edit banner',
			),
			'list' => array(
				'translationKey' => '_permission.banner.actions.list',
				'description'	 => 'List banners',
			),
			'place' => array(
				'translationKey' => '_permission.banner.actions.place',
				'description'	 => 'Place banner on pages',
			),
		),
	),
	
	// Zones manager
	'zone' => array(
		'translationKey' => '_permission.zone.description',
		'description'	 => 'Manage zones',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.zone.actions.add',
				'description'	 => 'Add new zone',
			),
			'delete' => array(
				'translationKey' => '_permission.zone.actions.delete',
				'description'	 => 'Delete zone',
			),
			'edit' => array(
				'translationKey' => '_permission.zone.actions.edit',
				'description'	 => 'Edit zone',
			),
			'list' => array(
				'translationKey' => '_permission.zone.actions.list',
				'description'	 => 'List zones',
			),
		),
	),
);
