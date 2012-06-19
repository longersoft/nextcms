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
 * @version		2011-10-18
 */

dojo.provide("core.js.base.Config");

core.js.base.Config.set = function(/*String*/ module, /*String*/ key, /*String*/ value) {
	// summary:
	//		Sets a module setting
	if (!core.js.base.Config._configs[module]) {
		core.js.base.Config._configs[module] = {};
	}
	core.js.base.Config._configs[module][key] = value;
};

core.js.base.Config.get = function(/*String*/ module, /*String*/ key, /*String*/ defaultValue) {
	// summary:
	//		Gets value of module setting
	if (!core.js.base.Config._configs[module]) {
		return defaultValue;
	}
	var value = core.js.base.Config._configs[module][key];
	return (value == null && defaultValue) ? defaultValue : value;	// Object
};

core.js.base.Config._configs = {};
