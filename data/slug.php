<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	data
 * @since		1.0
 * @version		2011-11-01
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * The array maps special characters with the replaced one
 * when generate the slug without specified language
 * 
 * @return array
 */
return array(
	'slug' => array(
		'á|à|ạ|ả|ã|ă|ắ|ằ|ặ|ẳ|ẵ|â|ấ|ầ|ậ|ẩ|ẫ|ä' => 'a',
		'Á|À|Ạ|Ả|Ã|Ă|Ắ|Ằ|Ặ|Ẳ|Ẵ|Â|Ấ|Ầ|Ậ|Ẩ|Ẫ|Ä' => 'A',
		'đ' => 'd',
		'Đ' => 'D',
		'é|è|ẹ|ẻ|ẽ|ê|ế|ề|ệ|ể|ễ' => 'e',
		'É|È|Ẹ|Ẻ|Ẽ|Ê|Ế|Ề|Ệ|Ể|Ễ' => 'E',
		'í|ì|ị|ỉ|ĩ' => 'i',
		'Í|Ì|Ị|Ỉ|Ĩ' => 'I',
		'ó|ò|ọ|ỏ|õ|ô|ố|ồ|ộ|ổ|ỗ|ơ|ớ|ờ|ợ|ở|ỡ' => 'o',
		'Ó|Ò|Ọ|Ỏ|Õ|Ô|Ố|Ồ|Ộ|Ổ|Ỗ|Ơ|Ớ|Ờ|Ợ|Ở|Ỡ' => 'O',
		'ú|ù|ụ|ủ|ũ|ư|ứ|ừ|ự|ử|ữ' => 'u',
		'Ú|Ù|Ụ|Ủ|Ũ|Ư|Ứ|Ừ|Ự|Ử|Ữ' => 'U',
		'ý|ỳ|ỵ|ỷ|ỹ' => 'y',
		'Ý|Ỳ|Ỵ|Ỷ|Ỹ' => 'Y',
	),
);
