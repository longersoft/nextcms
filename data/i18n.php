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
 * @version		2011-10-20
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * The array consists of all the available languages of the user interface.
 * Each language item has the following format:
 * 
 *		$locale => array(
 *			'english' => Name of the language in English
 *			'native'  => Name of the language in the country
 *		)
 * where $locale is combined by the language code (in lower case), '_' (without '),
 * and the country code (in upper case).
 * You can see the list of language and country codes in the following website:
 * http:download.oracle.com/docs/cd/E13214_01/wli/docs92/xref/xqisocodes.html
 * 
 * Administrator can update the language by going to the "Modules > Core > Configure module" 
 * menu in the back-end.
 * 
 * @return array
 */
return array(
	'en_US' => array(
		'english' => 'English',
		'native'  => 'English',
	),
	'vi_VN' => array(
		'english' => 'Vietnamese',
		'native'  => 'Tiếng Việt',
	),
);
