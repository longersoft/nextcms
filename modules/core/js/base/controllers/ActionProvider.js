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

dojo.provide("core.js.base.controllers.ActionProvider");

core.js.base.controllers.ActionProvider.set = function(/*String*/ name, /*Object*/ data) {
	// summary:
	//		Sets an action details
	// name:
	//		The name of action. It should be exactly the name of route that defines the controller action
	// data:
	//		Contains the following members:
	//		- name [String]: Name of action
	// 		- url [String]: The URL of action page
	//		- isAllowed [Boolean]: Indicates whether user have permission to access URL or not
	// Example usage:
	// |	core.js.base.controllers.ActionProvider.set("core_user_list", {
	// |		name: "core_user_list",
	// |		url: "/admin/core/user/list",
	// |		isAllowed: true
	// |	});
	core.js.base.controllers.ActionProvider._actions[name] = {
		name: name,
		url: data.url,
		isAllowed: data.isAllowed
	};
};

core.js.base.controllers.ActionProvider.get = function(/*String*/ name) {
	// summary:
	//		Gets the details of action by its name
	// name:
	//		The name of action, should be exactly the name of route
	if (!core.js.base.controllers.ActionProvider._actions[name]) {
		// The action has not been added to the collection
		throw new Error("Cannot find the action by name=" + name + ". You have to add the action to collection by using core.js.base.controllers.ActionProvider.set() method.");
	}
	return core.js.base.controllers.ActionProvider._actions[name];	// Object
};

// The object stores all the actions
core.js.base.controllers.ActionProvider._actions = {};
