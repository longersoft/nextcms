/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-02-28
 */

dojo.provide("message.js.views.FolderToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.FolderToolbar", null, {
	// _id: String
	_id: null,
	
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _i18n: Object
	_i18n: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		var toolbar = new dijit.Toolbar({}, this._id);
		var _this   = this;
		
		// "Add" button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.addAction,
			showLabel: false,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_folder_add").isAllowed,
			onClick: function(e) {
				_this.onAddFolder();
			}
		}));
		
		// "Refresh" button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_folder_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		}));
		
		toolbar.addChild(new dijit.ToolbarSeparator());
		
		// "Filter" button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.filterAction,
			showLabel: false,
			iconClass: "appIcon appFilterIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_filter_list").isAllowed,
			onClick: function(e) {
				_this.onListFilters();
			}
		}));
		
		// "Sort folders by name" button
		var sortButton = new dijit.form.Button({
			__ascending: false,
			label: this._i18n.global._share.sortAction,
			showLabel: false,
			iconClass: "appIcon appSortAscendingIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_folder_list").isAllowed,
			onClick: function(e) {
				_this.onSortAscending(this.__ascending);
				this.__ascending = this.__ascending ? false : true;
				this.set("iconClass", "appIcon " + (this.__ascending ? "appSortDescendingIcon" : "appSortAscendingIcon"));
			}
		});
		dojo.addClass(sortButton.domNode, "appRight");
		toolbar.addChild(sortButton);
	},
	
	////////// CALLBACKS //////////
	
	onAddFolder: function() {
		// summary:
		//		Adds new folder
		// tags:
		//		callback
	},
	
	onListFilters: function() {
		// summary:
		//		Lists message filters
		// tags:
		//		callback
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of folders
		// tags:
		//		callback
	},
	
	onSortAscending: function(/*Boolean*/ ascending) {
		// summmary:
		//		Sorts the list of folders by names
		// tags:
		//		callback
	}
});
