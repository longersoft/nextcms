<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	configs
 * @since		1.0
 * @version		2012-04-20
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Define the permissions of the Media module
 * 
 * @return array
 */
return array(
	// Manage albums
	'album' => array(
		'translationKey' => '_permission.album.description',
		'description'	 => 'Manage albums',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.album.actions.activate',
				'description'	 => 'Activate/deactivate album',
			),
			'add' => array(
				'translationKey' => '_permission.album.actions.add',
				'description'	 => 'Add new album',
			),
			'cover' => array(
				'translationKey' => '_permission.album.actions.cover',
				'description'	 => 'Set cover',
			),
			'delete' => array(
				'translationKey' => '_permission.album.actions.delete',
				'description'	 => 'Delete album',
			),
			'list' => array(
				'translationKey' => '_permission.album.actions.list',
				'description'	 => 'View the list of albums',
			),
			'rename' => array(
				'translationKey' => '_permission.album.actions.rename',
				'description'	 => 'Rename album',
			),
		),
	),
	
	// Config module
	'config' => array(
		'translationKey' => '_permission.config.description',
		'description'	 => 'Configure module',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.config.actions.config',
				'description'	 => 'Configure module',
			),
		),
	),
	
	// Import photos from Flickr
	'flickr' => array(
		'translationKey' => '_permission.flickr.description',
		'description'	 => 'Import photos from Flickr',
		'actions'		 => array(
			'import' => array(
				'translationKey' => '_permission.flickr.actions.import',
				'description'	 => 'Import photos',
			),
		),
	),
	
	// Manage photos
	'photo' => array(
		'translationKey' => '_permission.photo.description',
		'description'	 => 'Manage photos',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.photo.actions.activate',
				'description'	 => 'Activate/deactivate photo',
			),
			'copy' => array(
				'translationKey' => '_permission.photo.actions.copy',
				'description'	 => 'Copy photos to album',
			),
			'delete' => array(
				'translationKey' => '_permission.photo.actions.delete',
				'description'	 => 'Delete photo',
			),
			'download' => array(
				'translationKey' => '_permission.photo.actions.download',
				'description'	 => 'Download photo',
			),
			'edit' => array(
				'translationKey' => '_permission.photo.actions.edit',
				'description'	 => 'Edit photo',
			),
			'list' => array(
				'translationKey' => '_permission.photo.actions.list',
				'description'	 => 'View the list of photos',
			),
			'order' => array(
				'translationKey' => '_permission.photo.actions.order',
				'description'	 => 'Order photos in the album',
			),
			'remove' => array(
				'translationKey' => '_permission.photo.actions.remove',
				'description'	 => 'Remove photo from album',
			),
			'rename' => array(
				'translationKey' => '_permission.photo.actions.rename',
				'description'	 => 'Rename photo',
			),
			'replace' => array(
				'translationKey' => '_permission.photo.actions.replace',
				'description'	 => 'Replace photo',
			),
			'update' => array(
				'translationKey' => '_permission.photo.actions.update',
				'description'	 => 'Update photo information',
			),
			'upload' => array(
				'translationKey' => '_permission.photo.actions.upload',
				'description'	 => 'Upload photos',
			),
		),
	),
	
	// Manage playlists
	'playlist' => array(
		'translationKey' => '_permission.playlist.description',
		'description'	 => 'Manage playlists',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.playlist.actions.activate',
				'description'	 => 'Activate/deactivate playlist',
			),
			'add' => array(
				'translationKey' => '_permission.playlist.actions.add',
				'description'	 => 'Add new playlist',
			),
			'cover' => array(
				'translationKey' => '_permission.playlist.actions.cover',
				'description'	 => 'Set poster',
			),
			'delete' => array(
				'translationKey' => '_permission.playlist.actions.delete',
				'description'	 => 'Delete playlist',
			),
			'list' => array(
				'translationKey' => '_permission.playlist.actions.list',
				'description'	 => 'View the list of playlists',
			),
			'rename' => array(
				'translationKey' => '_permission.playlist.actions.rename',
				'description'	 => 'Rename playlist',
			),
		),
	),
	
	// Manage videos
	'video' => array(
		'translationKey' => '_permission.video.description',
		'description'	 => 'Manage videos',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.video.actions.activate',
				'description'	 => 'Activate/deactivate video',
			),
			'add' => array(
				'translationKey' => '_permission.video.actions.add',
				'description'	 => 'Add new video',
			),
			'copy' => array(
				'translationKey' => '_permission.video.actions.copy',
				'description'	 => 'Copy videos to playlist',
			),
			'cover' => array(
				'translationKey' => '_permission.video.actions.cover',
				'description'	 => 'Set poster',
			),
			'delete' => array(
				'translationKey' => '_permission.video.actions.delete',
				'description'	 => 'Delete video',
			),
			'download' => array(
				'translationKey' => '_permission.video.actions.download',
				'description'	 => 'Download video',
			),
			'list' => array(
				'translationKey' => '_permission.video.actions.list',
				'description'	 => 'View the list of videos',
			),
			'order' => array(
				'translationKey' => '_permission.video.actions.order',
				'description'	 => 'Order videos in the playlist',
			),
			'remove' => array(
				'translationKey' => '_permission.video.actions.remove',
				'description'	 => 'Remove video from playlist',
			),
			'rename' => array(
				'translationKey' => '_permission.video.actions.rename',
				'description'	 => 'Rename video',
			),
			'update' => array(
				'translationKey' => '_permission.video.actions.update',
				'description'	 => 'Update video information',
			),
		),
	),
);
