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
 * @version		2012-05-12
 */

dojo.provide("core.js.base.controllers.Subscriber");

core.js.base.controllers.Subscriber.subscribe = function(/*String*/ group, /*String*/ topic, /*Object*/ context, /*Function*/ callback) {
	var handlers = core.js.base.controllers.Subscriber._handlers;
	if (!handlers[group]) {
		handlers[group] = {};
	}
	handlers[group][topic] = dojo.subscribe(topic, context, callback);
	return handlers[group][topic];		// Object
};

core.js.base.controllers.Subscriber.unsubscribe = function(/*String*/ group, /*String?*/ topic) {
	var groupHandlers = core.js.base.controllers.Subscriber._handlers[group];
	if (groupHandlers) {
		if (topic) {
			dojo.unsubscribe(groupHandlers[topic]);
		} else {
			for (var i in groupHandlers) {
				dojo.unsubscribe(groupHandlers[i]);
			}
		}
	}
};

core.js.base.controllers.Subscriber.unsubscribeAll = function(/*String?*/ topic) {
	var handlers = core.js.base.controllers.Subscriber._handlers;
	for (var group in handlers) {
		if (topic) {
			dojo.unsubscribe(handlers[group][topic]);
		} else {
			core.js.base.controllers.Subscriber.unsubscribe(handlers[group]);
		}
	}
};

core.js.base.controllers.Subscriber._handlers = {};
