<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	templates
 * @package		default
 * @since		1.0
 * @version		2012-04-24
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Information of the default template
 * 
 * @return array
 */
return array(
	'name'		 => 'default',
	'author'	 => 'Nguyen Huu Phuoc',
	'email'		 => 'thenextcms@gmail.com',
	'version' 	 => '1.0',
	'appVersion' => '1.0+',
	'license'	 => 'http://nextcms.org/license.txt',
	'pages'		 => array(
		'content_article_archive',
		'content_article_category',
		'content_article_search',
		'content_article_tag',
		'content_article_view',
		'content_blog_archive',
		'content_blog_category',
		'content_blog_index',
		'content_blog_search',
		'content_blog_tag',
		'content_blog_view',
		'content_index_index',
		'content_page_view',
		'core_index_index',
		'file_attachment_download',
		'media_album_view',
		'media_index_index',
		'media_photo_search',
		'media_photo_tag',
		'media_photo_view',
		'media_playlist_view',
		'media_video_search',
		'media_video_tag',
		'media_video_view',
	),
);
