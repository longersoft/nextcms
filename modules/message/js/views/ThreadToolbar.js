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

dojo.provide("message.js.views.ThreadToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.ThreadToolbar", null, {
	// _id: String
	_id: null,

	// _i18n: Object
	_i18n: null,
	
	// _refreshButton: dijit.form.Button
	_refreshButton: null,
	
	// _searchTextBox: dijit.form.TextBox
	_searchTextBox: null,
	
	// _searchButton: dijit.form.Button
	_searchButton: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		
		// "Close" button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.closeAction,
			showLabel: false,
			iconClass: "appIcon appCancelIcon",
			onClick: function(e) {
				_this.onClose();
			}
		}));
		
		// "Refresh" button
		this._refreshButton = new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_view").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		});
		toolbar.addChild(this._refreshButton);
		
		toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Search control
		this._searchTextBox = new dijit.form.TextBox({
			style: "width: 150px",
			placeHolder: this._i18n.message.list.searchMessageHelp
		});
		this._searchButton = new dijit.form.Button({
			label: this._i18n.global._share.searchAction,
			showLabel: false,
			iconClass: "appIcon appSearchIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_view").isAllowed,
			onClick: function(e) {
				var keyword = _this._searchTextBox.get("value");
				_this.onSearchMesssages(keyword);
			}
		});
		toolbar.addChild(this._searchTextBox);
		toolbar.addChild(this._searchButton);
	},
	
	////////// CALLBACKS //////////
	
	onClose: function() {
		// summary:
		//		Closes the thread pane
		// tags:
		//		callback
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of private messages in the thread
		// tags:
		//		callback
	},
	
	onSearchMesssages: function(/*String*/ keyword) {
		// summary:
		//		Searches for private messages by keyword
		// tags:
		//		callback
	}
});
