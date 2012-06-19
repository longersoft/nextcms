/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	js
 * @since		1.0
 * @version		2012-06-12
 */

dojo.provide("core.js.base.Resource");

core.js.base.Resource.loadCss = function(/*String*/ path) {
	// summary:
	//		Loads a given CSS file
	if (dojo.query('link[type="text/css"][href="' + path + '"]').length == 0) {
		dojo.create("link", {
			href: path,
			media: "screen",
			rel: "stylesheet",
			type: "text/css"
		}, dojo.query("head")[0]);
	}
};

core.js.base.Resource.loadJs = function(/*String*/ path) {
	// summary:
	//		Loads a given JS file
	if (dojo.query('script[type="text/javascript"][src="' + path + '"]').length == 0) {
		dojo.create("script", {
			type: "text/javascript",
			src: path
		}, dojo.query("head")[0]);
	}
};
