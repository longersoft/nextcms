/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("file.js.views.PathBreadcrumb");

dojo.declare("file.js.views.PathBreadcrumb", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	constructor: function(/*String*/ id) {
		this._id 	  = id;
		this._domNode = dojo.byId(id);
	},
	
	show: function(/*String*/ path) {
		var _this = this;
		if (!path) {
			_this._domNode.innerHTML = "./";
			return;
		}
		_this._domNode.innerHTML = ".";
		
		var breadcrumb = ".";
		while (path[0] == "." || path[0] == "/") {
			path = path.substring(1);
		}
		dojo.forEach(path.split('/'), function(folder, index) {
			breadcrumb += "/" + folder;
			_this._domNode.innerHTML += "/";
			dojo.create("span", {
				innerHTML: folder,
				_breadcrumb: breadcrumb
			}, _this._domNode);
		});
		
		dojo.query("span", this._domNode).forEach(function(node, index) {
			dojo.connect(node, "onclick", function() {
				_this.onGotoPath(dojo.attr(node, "_breadcrumb"));
			});
		});
	},
	
	disconnect: function() {
		// summary:
		//		Disconnect. It should be called after disconnecting
		this._domNode.innerHTML = ".";
	},
	
	////////// CALLBACKS //////////
	
	onGotoPath: function(/*String*/ path) {
		// summary:
		//		This method is called when user click on each folder in the path
		// tags:
		//		callback
	}
});
